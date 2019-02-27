<?php

namespace School\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;

class CommentTable extends AbstractTableGateway
{
    protected $table ='comment';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Comment());
        $this->initialize();
    }
    
    public function fetchAll()
    {
        return $resultSet = $this->select();
    }
    
    public function fetchComments($id_school)
    {
        $filter = "`id_school` = $id_school";
        $result = $this->select($filter);
        return $result;
    }
    
    public function getComment($id)
    {
        $id  = (int) $id;
        
        if($id) {
            $rowset = $this->select(array('id' => $id));
            $row = $rowset->current();
        }
        else {
            //$rowset = $this->select(array('id' => '1'));
            //$rowset = new ResultSet();
            //$row = $rowset->current();
            $row = $this->resultSetPrototype->getArrayObjectPrototype();
            $row->id = 0;
            $row->id_school = 0;
            $row->author = 'Гость';
            $row->text = '';
            $row->time = 0;
            $row->visible = 1;
        } 
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveComment(Comment $comment)
    {
        if ($comment->id === 0) {
            if($comment->text !=='') {
                $comment->author = ($comment->author) ? $comment->author : 'Гость';
                $data = array(
                    'id' => 0,
                    'id_school' => $comment->id_school,
                    'time' => time(),
                    'author' => $comment->author,
                    'text'  => $comment->text,
                    'visible' => 1,
                );
                $this->insert($data);
            }
        }
        elseif ($this->getComment($comment->id)) {
            $data = array(
                'visible' => $comment->visible,
            );
            $this->update(
                $data,
                array(
                    'id' => $comment->id,
                )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteComment($id)
    {
        $this->delete(array(
            'id' => $id,
        ));
    }
}