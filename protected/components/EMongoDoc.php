<?php
/**
 * feeds model
 * Authors: kexianbing
 * Date: 2014-03-20
 */
abstract class EMongoDoc extends EMongoDocument
{
    public function getCollectionName()
    {
        return lcfirst(get_called_class());
    }

    //override setAttributes() to trigger validate and get all errors
    //fill object with data array
    public function setAttributes($data, $safeOnly=true)
    {
        $rtn=array();
        parent::setAttributes($data, $safeOnly);
        if (!$this->validate())
        {
           $rtn=$this->getErrors();
        }
        return $rtn;
    }

    //5 common validators:
    public function validate_each($attr_name, $params)
    {
        $params['allowEmpty']=isset($params['allowEmpty'])?$params['allowEmpty']:true;
        if ($this->$attr_name==null)
        {
            if (!$params['allowEmpty'])
            {
                $this->addError($attr_name, "Don't allow empty for ".$attr_name);
            }
            return;
        }
       if (!is_array($this->$attr_name))
        {
            $this->addError($attr_name, '该字段应是数组');
            return;
        }
        foreach ($this->$attr_name as $v)
        {
            if (!$this->$params['function']($v))
            {
                $this->addError($attr_name, $params['message']);
            }
        }
    }

    public function validate_model($attr_name, $params)
    {
        $params['allowEmpty']=isset($params['allowEmpty'])?$params['allowEmpty']:true;
        if ($this->$attr_name==null)
        {
            if (!$params['allowEmpty'])
            {
                $this->addError($attr_name, "Don't allow empty for ".$attr_name);
            }
            return;
        }
        if (!is_array($this->$attr_name)) //because CModel->setAttributes($v) won't report erro (only "return;") when $v is not array
        {
            $this->addError($attr_name, '该字段应是对象');
            return;
        }
        $obj=new $params['model']();
        $obj->setAttributes($this->$attr_name);
        if (!$obj->validate())
        {
            $this->addError($attr_name, $params['message'].':'.json_encode($obj->getErrors()));
        }
    }

    public function validate_model_each($attr_name, $params)
    {
        $params['allowEmpty']=isset($params['allowEmpty'])?$params['allowEmpty']:true;
        if ($this->$attr_name==null)
        {
            if (!$params['allowEmpty'])
            {
                $this->addError($attr_name, "Don't allow empty for ".$attr_name);
            }
            return;
        }
        if (!is_array($this->$attr_name))
        {
            $this->addError($attr_name, '该字段应是数组');
            return;
        }
        foreach ($this->$attr_name as $v)
        {
            if (!is_array($v)) //because CModel->setAttributes($v) won't report erro (only "return;") when $v is not array
            {
                $this->addError($attr_name, '该数组内元素应是对象');
                return;
            }
            $obj=new $params['model']();
            $obj->setAttributes($v);
            if (!$obj->validate())
            {
                $this->addError($attr_name, $params['message'].':'.json_encode($obj->getErrors()));
                return;
            }
        }
    }

    public function require_one($attr_names_pipe, $params)
    {
        $attr_names=explode('|', $attr_names_pipe);
        $attr_names=array_map('trim', $attr_names);
        foreach ($attr_names as $attr_name)
        {
            if (!empty($this->$attr_name))
            {
                return;
            }
        }
        $this->addError($attr_names_pipe, $params['message']);
    }

    public function match_each($attr_name, $params)
    {
        $params['allowEmpty']=isset($params['allowEmpty'])?$params['allowEmpty']:true;
        if ($this->$attr_name==null)
        {
            if (!$params['allowEmpty'])
            {
                $this->addError($attr_name, "Don't allow empty for ".$attr_name);
            }
            return;
        }
        if (!is_array($this->$attr_name))
        {
            $this->addError($attr_name, $params['message']);
            return;
        }
        foreach ($this->$attr_name as $v)
        {
            if (!preg_match($params['pattern'], $v))
            {
                $this->addError($attr_name, $params['message']);
                return;
            }
        }
    }

    //in Yii mongo suite,
    //there are "insert" and "replace" in save(),
    //there are "insert/replace" and "update" in update(),
    //but there isn't "insert/update", so patched the 2 functions,
    //so we can always use save(true, null, true) to trigger afterSave() when we need update()
    public function save($runValidation=true, $attributes=null, $modify=false, $upsert=false)
    {
        if ($modify)
        {
            $this->setIsNewRecord(false);
        }
        if ($runValidation)
        {
            $validated=$this->validate($attributes);
            if (!$validated)
            {
                throw APIException::invalidInputFormat($this->getErrors());
            }
        }
        return ($this->getIsNewRecord()) ? $this->insert($attributes) : $this->update($attributes, $modify, $upsert);
    }

    public function update(array $attributes=null, $modify = false, $upsert=false)
    {
        if($this->getIsNewRecord())
            throw new CDbException(Yii::t('yii','The EMongoDocument cannot be updated because it is new.'));
        if($this->beforeSave())
        {
            Yii::trace(get_class($this).'.update()','ext.MongoDb.EMongoDocument');
            $rawData = $this->toArray();
            // filter attributes if set in param
            if($attributes!==null)
            {
                if (!in_array('_id', $attributes) && !$modify) $attributes[] = '_id'; // This is very easy to forget

                foreach($rawData as $key=>$value)
                {
                    if(!in_array($key, $attributes))
                        unset($rawData[$key]);
                }
            }

            if($modify)
            {
                //get rid of null in $this->toArray()
                foreach($rawData as $key=>$value)
                {
                    if ($value===null)
                    {
                        unset($rawData[$key]);
                    }
                }
                if(isset($rawData['_id']) === true)
                    unset($rawData['_id']);
                $result = $this->getCollection()->update(
                    array('_id' => $this->_id),
                    array('$set' => $rawData),
                    array('upsert'=>$upsert,
                        'fsync'=>$this->getFsyncFlag(),
                        'multiple'=>false
                    )
                );
            } else {
                if(version_compare(Mongo::VERSION, '1.0.5','>=') === true)
                    $result = $this->getCollection()->save($rawData, array(
                        'fsync'=>$this->getFsyncFlag(),
                    ));
                else
                    $result = $this->getCollection()->save($rawData);
            }

            if($result !== false) // strict comparison needed
            {
                $this->afterSave();

                return true;
            }

            throw new CException(Yii::t('yii', 'Can\t save document to disk, or try to save empty document!'));
        }
    }

    //override to make findAll() return array that indexed with specified fields, for example '_id', 'uid' etc
    public function findAll($criteria=null, $index=null)
    {
        Yii::trace(get_class($this).'.findAll()','ext.MongoDb.EMongoDocument');

        if($this->beforeFind())
        {
            $this->applyScopes($criteria);

            $cursor = $this->getCollection()->find($criteria->getConditions());

            if($criteria->getSort() !== null)
                $cursor->sort($criteria->getSort());
            if($criteria->getLimit() !== null)
                $cursor->limit($criteria->getLimit());
            if($criteria->getOffset() !== null)
                $cursor->skip($criteria->getOffset());
            if($criteria->getSelect())
                $cursor->fields($criteria->getSelect(true));

            if($this->getUseCursor())
                return new EMongoCursor($cursor, $this->model());
            else
                return $this->populateRecords($cursor, true, $index);    //modified this line
        }
        return array();
    }

}