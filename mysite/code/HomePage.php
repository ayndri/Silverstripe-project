<?php

class HomePage extends Page {}

class HomePage_Controller extends Page_Controller
{
    public function LatestArticles($count = 3)
    {
        return ArticlePage::get()
            ->sort('Created', 'DESC')
            ->limit($count);
    }

    public function FeaturedProperties($limit = 6)
    {
        return Property::get()
            ->filter(array(
                'FeaturedOnHomepage' => true
            ))
            ->limit($limit);
    }
}
