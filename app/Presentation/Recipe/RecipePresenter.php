<?php

namespace App\Presentation\Recipe;

use App\Model\CategoryFacade;
use App\Model\RecipeFacade;
use App\Model\CommentFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;
use Nette\Http\FileUpload;

final class RecipePresenter extends Presenter
{
    private ?ActiveRow $editingRecipe = null;

    public function __construct(
        private RecipeFacade $recipeFacade,
        private CategoryFacade $categoryFacade,
        private CommentFacade $commentFacade,
    ) {}

    public function renderDefault(?int $category = null): void
    {
        if ($category !== null) {
            $this->template->recipes = $this->recipeFacade->getByCategory($category);
            $this->template->categoryName = $this->categoryFacade->getById($category)?->name ?? 'Neznámá kategorie';
        } else {
            $this->template->recipes = $this->recipeFacade->getAll();
            $this->template->categoryName = 'Všechny recepty';

        }
    }

    public function renderAdd(): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Musíš být přihlášený.', 'warning');
            $this->redirect('Homepage:default');
        }
    }

    public function renderEdit(int $id): void
    {
        $recipe = $this->recipeFacade->getById($id);
        if (!$recipe) {
            $this->error('Recept nenalezen.');
        }
        if (
            !$this->getUser()->isInRole('admin') &&
            $recipe->author_id !== $this->getUser()->getId()
        ) {
            $this->flashMessage('Nemáš oprávnění upravit tento recept.', 'error');
            $this->redirect('default');
        }
    
        $this->editingRecipe = $recipe;
    
        $this['recipeForm']->setDefaults([
            'id' => $recipe->id,
            'title' => $recipe->title,
            'content' => $recipe->content,
            'category_id' => $recipe->category_id,
            'duration' => $recipe->duration,
            'difficulty' => $recipe->difficulty,
            'ingredients' => implode(', ', json_decode($recipe->ingredients, true)),
            'comments_enabled' => $recipe->comments_enabled,
        ]);
    }
    

    protected function createComponentRecipeForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');
        $form->addText('title', 'Název receptu:')->setRequired();
        $form->addTextArea('content', 'Popis:')->setRequired();
        $form->addUpload('image', 'Obrázek:')
            ->addCondition(Form::FILLED)
            ->addRule(Form::IMAGE, 'Musí být obrázek JPG nebo PNG.');
        $form->addSelect('category_id', 'Kategorie:', $this->categoryFacade->getAll()->fetchPairs('id', 'name'))
            ->setPrompt('Vyber kategorii')->setRequired();
        $form->addInteger('duration', 'Doba přípravy (minuty):')->setRequired();
        $form->addSelect('difficulty', 'Obtížnost:', [
            'lehký' => 'Lehký',
            'střední' => 'Střední',
            'těžký' => 'Těžký',
        ])->setRequired();
        $form->addTextArea('ingredients', 'Suroviny (odděluj čárkou):')->setRequired();
        $form->addCheckbox('comments_enabled', 'Povolit komentáře')->setDefaultValue(true);
        $form->addSubmit('send', 'Uložit recept');

        $form->onSuccess[] = function (Form $form, array $values): void {
            $image = $values['image'];
            $imageName = null;

            if ($image instanceof FileUpload && $image->isOk()) {
                $imageName = uniqid() . '.' . $image->getImageFileExtension();
                $image->move(__DIR__ . '/../../../www/uploads/' . $imageName);
            }

            $ingredients = array_map('trim', explode(',', $values['ingredients']));

            if (!empty($values['id'])) {
                $updateData = [
                    'title' => $values['title'],
                    'content' => $values['content'],
                    'category_id' => $values['category_id'],
                    'duration' => $values['duration'],
                    'difficulty' => $values['difficulty'],
                    'ingredients' => $ingredients,
                    'comments_enabled' => $values['comments_enabled'],
                ];

                if ($imageName !== null) {
                    $updateData['image'] = $imageName;
                }

                $this->recipeFacade->update($values['id'], $updateData);
                $this->flashMessage('Recept byl upraven.', 'success');
            } else {
                $this->recipeFacade->add([
                    'title' => $values['title'],
                    'content' => $values['content'],
                    'image' => $imageName,
                    'author_id' => $this->getUser()->getId(),
                    'category_id' => $values['category_id'],
                    'duration' => $values['duration'],
                    'difficulty' => $values['difficulty'],
                    'ingredients' => $ingredients,
                    'comments_enabled' => $values['comments_enabled'],
                ]);
                $this->flashMessage('Recept byl přidán.', 'success');
            }

            $this->redirect('default');
        };

        return $form;
    }

    public function renderDetail(int $id): void
    {
        $recipe = $this->recipeFacade->getById($id);
        if (!$recipe) {
            $this->error('Recept nenalezen.');
        }

        $this->recipeFacade->incrementViews($id);

        $this->template->recipe = $recipe;
        $this->template->ingredients = json_decode($recipe->ingredients, true);
        $this->template->averageRating = $this->recipeFacade->getAverageRating($id);
        $this->template->comments = $this->commentFacade->getByRecipe($id);
    }

    protected function createComponentRatingForm(): Form
    {
        $form = new Form();
        $form->addSelect('rating', 'Hodnocení:', [1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])
            ->setPrompt('Zvolte počet hvězdiček')
            ->setRequired();

        $form->addSubmit('send', 'Ohodnotit');

        $form->onSuccess[] = function (Form $form, $values): void {
            $recipeId = $this->getParameter('id');
            $this->recipeFacade->addRating((int) $recipeId, (int) $values->rating);
            $this->flashMessage('Díky za hodnocení!', 'success');
            $this->redirect('this');
        };

        return $form;
    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form();
    
        $form->addTextArea('content', 'Komentář:')
            ->setRequired('Napiš něco!');
    
        $form->addSubmit('send', 'Odeslat komentář');
    
        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            $recipeId = (int) $this->getParameter('id');
            $authorId = (int) $this->getUser()->getId();
    
            $this->commentFacade->add($recipeId, $authorId, $values->content);
            $this->flashMessage('Komentář uložen.', 'success');
            $this->redirect('this');
        };
    
        return $form;
    }
    
    

    public function actionDelete(int $id): void
    {
        $recipe = $this->recipeFacade->getById($id);
        if (!$recipe) {
            $this->error('Recept nenalezen.');
        }

        if (
            !$this->getUser()->isInRole('admin') &&
            $recipe->author_id !== $this->getUser()->getId()
        ) {
            $this->flashMessage('Nemáš oprávnění smazat tento recept.', 'error');
            $this->redirect('default');
        }
    
        $this->recipeFacade->delete($id);
        $this->flashMessage('Recept byl smazán.', 'success');
        $this->redirect('default');
    }
    
}