<?php

class ArticleCategory extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar'
    );

    public static $has_one = array(
        'ArticleHolder' => 'ArticleHolder'
    );

    public static $belongs_many_many = array(
        'Articles' => 'ArticlePage'
    );

    public function getCMSFields()
    {
        return FieldList::create(
            TextField::create('Title')
        );
    }

    public function Link()
    {
        return $this->ArticleHolder()->Link('category/' . $this->ID);
    }
}
