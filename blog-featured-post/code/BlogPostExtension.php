<?php

class BlogPostExtension extends DataExtension {
    private static $db = array(
        'IsFeatured' => 'Boolean'
    );

    public function updateCMSFields(FieldList $fields) {
        $sidebar = $fields->fieldByName('blog-admin-sidebar');
        $sidebar->addFieldToTab('PublishDate', CheckboxField::create('IsFeatured'));
    }
}
