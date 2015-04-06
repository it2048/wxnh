<?php

/**
 * This is the model class for table "sms_notice".
 *
 * The followings are the available columns in table 'sms_notice':
 * @property string $telorsb
 * @property integer $ftime
 * @property integer $ctn
 * @property integer $ltime
 */
class SmsNotice extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsNotice the static model class
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
		return 'sms_notice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('telorsb, ftime, ctn, ltime', 'required'),
			array('ftime, ctn, ltime', 'numerical', 'integerOnly'=>true),
			array('telorsb', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('telorsb, ftime, ctn, ltime', 'safe', 'on'=>'search'),
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
			'telorsb' => 'Telorsb',
			'ftime' => 'Ftime',
			'ctn' => 'Ctn',
			'ltime' => 'Ltime',
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

		$criteria->compare('telorsb',$this->telorsb,true);
		$criteria->compare('ftime',$this->ftime);
		$criteria->compare('ctn',$this->ctn);
		$criteria->compare('ltime',$this->ltime);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}