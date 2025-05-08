<?php

namespace App\Presentation\Forum;

use App\Model\ForumFacade;
use App\Model\ForumCommentFacade;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;
use Nette\Security\User;

final class ForumPresenter extends Presenter
{
    private ?ActiveRow $editingPost = null;

    public function __construct(
        private ForumFacade $forumFacade,
        private ForumCommentFacade $commentFacade,
    ) {}


    public function renderDefault(): void
    {
        $this->template->posts = $this->forumFacade->getAll();
    }

    public function renderAdd(): void
    {
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('Pro přidání příspěvku se musíš přihlásit.', 'warning');
            $this->redirect('Auth:in');
        }
    }

    public function renderEdit(int $id): void
    {
        $post = $this->forumFacade->getById($id);
    
        if (!$post) {
            $this->error('Příspěvek nenalezen.');
        }
    
        $user = $this->getUser();
        if ($user->getId() !== $post->author_id && !$user->isInRole('admin')) {
            $this->error('Nemáš oprávnění upravovat tento příspěvek.');
        }
    
        $this['editForm']->setDefaults([
            'title' => $post->title,
            'content' => $post->content,
        ]);
    }
    

    public function renderDetail(int $id): void
    {
        $post = $this->forumFacade->getById($id);
        if (!$post) {
            $this->error('Příspěvek nebyl nalezen.');
        }
        $this->template->post = $post;
        $this->template->comments = $this->commentFacade->getByPost($id);
        
    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form();
        $form->addTextArea('content', 'Komentář:')
            ->setRequired('Napiš svůj komentář.');

        $form->addSubmit('send', 'Odeslat');

        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            $postId = (int) $this->getParameter('id');
            $authorId = (int) $this->getUser()->getId();

            $this->commentFacade->add($postId, $authorId, $values->content);
            $this->flashMessage('Komentář byl uložen.', 'success');
            $this->redirect('this');
        };

        return $form;
    }


    protected function createComponentForumForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');
        $form->addText('title', 'Nadpis:')
            ->setRequired('Zadej nadpis.');
        $form->addTextArea('content', 'Obsah:')
            ->setRequired('Napiš nějaký obsah.');
        $form->addSubmit('send', 'Uložit');

        $form->onSuccess[] = function (Form $form, \stdClass $values): void {
            $data = [
                'title' => $values->title,
                'content' => $values->content,
            ];

            if (!empty($values->id)) {
                $this->forumFacade->update((int) $values->id, $data);
                $this->flashMessage('Příspěvek upraven.', 'success');
            } else {
                $data['author_id'] = $this->user->getId();
                $this->forumFacade->add($data);
                $this->flashMessage('Příspěvek přidán.', 'success');
            }

            $this->redirect('Forum:default');
        };

        return $form;
    }

    protected function createComponentEditForm(): \Nette\Application\UI\Form
    {
        $form = new \Nette\Application\UI\Form;

        $form->addText('title', 'Název příspěvku:')
            ->setRequired('Zadej název příspěvku.');

        $form->addTextArea('content', 'Obsah:')
            ->setRequired('Zadej obsah příspěvku.');

        $form->addSubmit('send', 'Uložit změny');

        $form->onSuccess[] = function (\Nette\Application\UI\Form $form, \stdClass $values): void {
            $postId = (int) $this->getParameter('id');
            $post = $this->forumFacade->getById($postId);

            if (!$post) {
                $this->error('Příspěvek nenalezen.');
            }

            $user = $this->getUser();
            if ($user->getId() !== $post->author_id && !$user->isInRole('admin')) {
                $this->error('Nemáš oprávnění upravovat tento příspěvek.');
            }

            $this->forumFacade->update($postId, [
                'title' => $values->title,
                'content' => $values->content,
            ]);

            $this->flashMessage('Příspěvek byl upraven.', 'success');
            $this->redirect('Forum:default');
        };

        return $form;
    }


    public function actionDelete(int $id): void
    {
        $post = $this->forumFacade->getById($id);
        if (!$post) {
            $this->error('Příspěvek nebyl nalezen.');
        }

        // Ověření oprávnění
        if ($post->author_id !== $this->user->getId() && !$this->user->isInRole('admin')) {
            $this->flashMessage('Nemáš oprávnění smazat tento příspěvek.', 'error');
            $this->redirect('Forum:default');
        }

        $this->forumFacade->delete($id);
        $this->flashMessage('Příspěvek smazán.', 'success');
        $this->redirect('Forum:default');
    }
}
