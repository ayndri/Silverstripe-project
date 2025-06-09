<?php

class Region extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar',
        'Description' => 'HTMLText'
    );

    private static $has_one = array(
        'Photo' => 'Image',
        'RegionsPage' => 'RegionsPage'
    );

    private static $has_many = array(
        'Articles' => 'ArticlePage'
    );

    private static $summary_fields = array(
        'Photo.CMSThumbnail' => '',
        'Photo.Filename' => 'Photo file name',
        'Title' => 'Title of region',
        'Description' => 'Short description',
    );

    public function getGridThumbnail()
    {
        if ($this->Photo()->exists()) {
            return $this->Photo()->setWidth(100);
        }

        return '(no image)';
    }

    public function getCMSFields()
    {
        $fields = FieldList::create(
            TextField::create('Title'),
            HtmlEditorField::create('Description'),
            $uploader = UploadField::create('Photo')
        );

        $uploader->setFolderName('region-photos');
        $uploader->getValidator()->setAllowedExtensions(array('png', 'jpg', 'jpeg', 'gif'));

        return $fields;
    }

    public function Link()
    {
        return $this->RegionsPage()->Link('show/' . $this->ID);
    }

    public function LinkingMode()
    {
        return Controller::curr()->getRequest()->param('ID') == $this->ID ? 'current' : 'link';
    }

    public function ArticlesLink()
    {
        $page = ArticleHolder::get()->first();

        if ($page) {
            return $page->Link('region/' . $this->ID);
        }
    }
}
