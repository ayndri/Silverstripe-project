<?php

class ArticlePage extends Page
{
    private static $db = array(
        'Date' => 'Date',
        'Teaser' => 'Text',
        'Author' => 'Varchar',
    );

    private static $can_be_root = false;

    private static $has_one = array(
        'Photo' => 'Image',
        'Brochure' => 'File',
        'Region' => 'Region'
    );

    private static $many_many = array(
        'Categories' => 'ArticleCategory'
    );

    private static $has_many = array(
        'Comments' => 'ArticleComment'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab(
            'Root.Main',
            DateField::create('Date', 'Date of Article')
                ->setConfig('showcalendar', true),
            'Content'
        );
        $fields->addFieldsToTab('Root.Main', TextareaField::create('Teaser'), 'Content');
        $fields->addFieldsToTab(
            'Root.Main',
            TextField::create('Author', 'Author of Article')
                ->setDescription("If multiple authors, separate with commas")
                ->setMaxLength(50),
            'Content'
        );
        $fields->addFieldsToTab('Root.Attachments', $photo = UploadField::create('Photo'));
        $fields->addFieldsToTab('Root.Attachments', $brochure = UploadField::create('Brochure', 'Travel brochure, optional (PDF only)'));

        $photo->getValidator()->setAllowedExtensions(array('png', 'jpg', 'jpeg', 'gif'));
        $photo->setFolderName('travel-photos');

        $brochure->getValidator()->setAllowedExtensions(array('pdf'));
        $brochure->setFolderName('travel-brochures');

        $fields->addFieldsToTab('Root.Categories', CheckboxSetField::create(
            'Categories',
            'Selected Categories',
            $this->Parent()->Categories()->map('ID', 'Title')
        ));

        $fields->addFieldsToTab('Root.Main', DropdownField::create(
            'RegionID',
            'Region',
            Region::get()->map('ID', 'Title')
        )->setEmptyString('-- None --'), 'Content');

        return $fields;
    }

    public function CategoriesList()
    {
        if ($this->Categories()->exists()) {
            return implode(', ', $this->Categories()->column('Title'));
        }
    }
}

class ArticlePage_Controller extends Page_Controller
{
    private static $allowed_actions = array(
        'CommentForm',
        'handleComment'
    );

    public function CommentForm()
    {
        $form = Form::create(
            $this,
            'CommentForm',
            FieldList::create(
                TextField::create('Name', ''),
                EmailField::create('Email', ''),
                TextareaField::create('Comment', '')
            ),
            FieldList::create(
                FormAction::create('handleComment', 'Post Comment')
                    ->setUseButtonTag(true)
                    ->addExtraClass('btn btn-default-color btn-lg')
            ),
            RequiredFields::create('Name', 'Email', 'Comment')
        )->addExtraClass('form-style');

        foreach ($form->Fields() as $field) {
            $field->setAttribute('placeholder', $field->getName() . '*')
                ->setAttribute('class', 'form-control')
                ->addExtraClass('col-sm-12 px-0');
        }

        $data = Session::get("FormData.CommentForm.data");
        return $data ? $form->loadDataFrom($data) : $form;
    }


    public function handleComment($data, $form)
    {
        Session::set("FormData.CommentForm.data", $data);
        $existing = $this->Comments()->filter(array(
            'Comment' => $data['Comment'],
        ));

        if ($existing->exists()) {
            $form->sessionMessage('That comment already exists!', 'bad');
            return $this->redirectBack();
        } else if (strlen($data['Comment']) < 20) {
            $form->sessionMessage('Your comment is too short!', 'bad');
            return $this->redirectBack();
        }
        $comment = ArticleComment::create();
        $comment->ArticlePageID = $this->ID;
        $form->saveInto($comment);
        $comment->write();

        Session::clear("FormData.CommentForm.data");
        $form->sessionMessage('Thanks for your comment', 'good');
        return $this->redirectBack();
    }
}
