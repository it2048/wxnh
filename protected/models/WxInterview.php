<?php

/**
 * This is the model class for table "wx_interview".
 *
 * The followings are the available columns in table 'wx_interview':
 * @property string $id
 * @property integer $month
 * @property integer $brand
 * @property string $dm
 * @property string $zmzy
 * @property string $city
 * @property string $am_sge
 * @property integer $am_time
 * @property string $am_add
 * @property integer $am_people
 * @property string $oje_ct
 * @property integer $oje_time
 * @property string $oje_add
 * @property integer $oje_people
 * @property integer $dm_time
 * @property string $dm_add
 * @property integer $dm_people
 */
class WxInterview extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WxInterview the static model class
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
		return 'wx_interview';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dm, zmzy, city, am_sge, am_add, oje_ct, oje_add, dm_add', 'required'),
			array('month, brand, am_time, am_people, oje_time, oje_people, dm_time, dm_people', 'numerical', 'integerOnly'=>true),
			array('dm, city, am_sge', 'length', 'max'=>45),
			array('zmzy', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, month, brand, dm, zmzy, city, am_sge, am_time, am_add, am_people, oje_ct, oje_time, oje_add, oje_people, dm_time, dm_add, dm_people', 'safe', 'on'=>'search'),
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
			'month' => 'Month',
			'brand' => 'Brand',
			'dm' => 'Dm',
			'zmzy' => 'Zmzy',
			'city' => 'City',
			'am_sge' => 'Am Sge',
			'am_time' => 'Am Time',
			'am_add' => 'Am Add',
			'am_people' => 'Am People',
			'oje_ct' => 'Oje Ct',
			'oje_time' => 'Oje Time',
			'oje_add' => 'Oje Add',
			'oje_people' => 'Oje People',
			'dm_time' => 'Dm Time',
			'dm_add' => 'Dm Add',
			'dm_people' => 'Dm People',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('month',$this->month);
		$criteria->compare('brand',$this->brand);
		$criteria->compare('dm',$this->dm,true);
		$criteria->compare('zmzy',$this->zmzy,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('am_sge',$this->am_sge,true);
		$criteria->compare('am_time',$this->am_time);
		$criteria->compare('am_add',$this->am_add,true);
		$criteria->compare('am_people',$this->am_people);
		$criteria->compare('oje_ct',$this->oje_ct,true);
		$criteria->compare('oje_time',$this->oje_time);
		$criteria->compare('oje_add',$this->oje_add,true);
		$criteria->compare('oje_people',$this->oje_people);
		$criteria->compare('dm_time',$this->dm_time);
		$criteria->compare('dm_add',$this->dm_add,true);
		$criteria->compare('dm_people',$this->dm_people);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}