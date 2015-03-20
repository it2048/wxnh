<?php

/**
 * This is the model class for table "wx_msg".
 *
 * The followings are the available columns in table 'wx_msg':
 * @property integer $id
 * @property string $receive_id
 * @property integer $tm
 * @property string $type
 * @property string $content
 * @property string $send_id
 */
class Msg extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Msg the static model class
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
		return 'wx_msg';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('receive_id, tm, type, content, send_id', 'required'),
			array('tm', 'numerical', 'integerOnly'=>true),
			array('receive_id, send_id', 'length', 'max'=>64),
			array('type', 'length', 'max'=>16),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, receive_id, tm, type, content, send_id', 'safe', 'on'=>'search'),
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
			'receive_id' => 'Receive',
			'tm' => 'Tm',
			'type' => 'Type',
			'content' => 'Content',
			'send_id' => 'Send',
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
		$criteria->compare('receive_id',$this->receive_id,true);
		$criteria->compare('tm',$this->tm);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('send_id',$this->send_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    /**
     * Store the message from user send
     * @param type $arr 
     */
    public function insertReceive($arr)
    {
        $this->receive_id = $arr->ToUserName;
        $this->tm = $arr->CreateTime;
        $this->type = $arr->MsgType;
        $this->content = $arr->Content;
        $this->send_id = $arr->FromUserName;
        $this->setIsNewRecord(true);
        $this->save();
    }
    /**
     * Store the message from user send
     * @param type $arr 
     */
    public function insertSend($arr)
    {
        $this->receive_id = $arr['ToUserName'];
        $this->tm = $arr['CreateTime'];
        $this->type = $arr['MsgType'];
        $this->content = $arr['Content'];
        $this->send_id = $arr['FromUserName'];
        $this->setIsNewRecord(true);
        $this->save();
    }
}