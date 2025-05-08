<?php

namespace App\Presentation\Category;

use App\Model\CategoryFacade;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class CategoryPresenter extends Presenter
{
    public function __construct(
        private CategoryFacade $categoryFacade,
    ) {}

    public function renderDefault(): void
    {
        $this->template->categories = $this->categoryFacade->getAll();
    }

    public function renderAdd(): void {}

    public function renderEdit(int $id): void
    {
        $category = $this->categoryFacade->getById($id);
        if (!$category) {
            $this->error('Kategorie nenalezena.');
        }

        $this['categoryForm']->setDefaults([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }

    protected function createComponentCategoryForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');

        $form->addText('name', 'Název kategorie:')
            ->setRequired('Zadej název kategorie.');

        $form->addTextArea('description', 'Popis:')
            ->setNullable();

        $form->addSubmit('send', 'Uložit kategorii');

        $form->onSuccess[] = function (Form $form, array $values): void {
            if (!empty($values['id'])) {
                $this->categoryFacade->update(
                    (int) $values['id'],
                    $values['name'],
                    $values['description']
                );
                $this->flashMessage('Kategorie byla upravena.', 'success');
            } else {
                $this->categoryFacade->add($values['name'], $values['description']);
                $this->flashMessage('Kategorie byla přidána.', 'success');
            }

            $this->redirect('default');
        };

        return $form;
    }

    public function handleDelete(int $id): void
    {
        $this->categoryFacade->delete($id);
        $this->flashMessage('Kategorie byla smazána.', 'success');
        $this->redirect('this');
    }
}
