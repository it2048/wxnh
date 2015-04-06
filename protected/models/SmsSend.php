<?php

/**
 * This is the model class for table "sms_send".
 *
 * The followings are the available columns in table 'sms_send':
 * @property integer $id
 * @property string $tel
 * @property string $content
 * @property integer $time
 * @property string $type
 * @property integer $rtn
 */
class SmsSend extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsSend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sms_send';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('time, rtn', 'numerical', 'integerOnly'=>true),
			array('tel', 'length', 'max'=>45),
			array('content', 'length', 'max'=>320),
			array('type', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tel, content, time, type, rtn', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tel' => 'Tel',
			'content' => 'Content',
			'time' => 'Time',
			'type' => 'Type',
			'rtn' => 'Rtn',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('time',$this->time);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('rtn',$this->rtn);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}