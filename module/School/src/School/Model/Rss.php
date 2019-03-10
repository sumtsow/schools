<?php
namespace School\Model;

use Zend\Feed\Reader\Reader;
use Zend\Feed\Exception\Reader\RuntimeException;

class Rss
{
	private $channel;
        private $myRss;

    public function __construct($href) {
                    
        try {
            $this->myRss = Reader::import($href);
        }
        
        catch (RuntimeException $e) {
            // feed import failed
            echo "Exception caught importing feed: {$e->getMessage()}\n";
            exit;
        }

        // Initialize the channel/feed data array
        $this->channel = array(
            'title'       => $this->myRss->getTitle(),
            'link'        => $this->myRss->getLink(),
            'description' => $this->myRss->getDescription(),
            'items'        => array()
        );

        // Loop over each channel item/entry and store relevant data for each
        foreach ($this->myRss as $item) {
            $this->channel['items'][] = [
                'title'       => $item->getTitle(),
                'link'        => $item->getLink(),
                'description' => $item->getDescription(),
                'language'    => $this->myRss->getLanguage(),
                'enclosure'   => ($item->getEnclosure()) ? $item->getEnclosure() : null,
                'pubDate'     => get_object_vars($item->getDateModified()),
                ];
        }
    }
    
    public function __get($attr) {
        return $this->$attr;
    }
}