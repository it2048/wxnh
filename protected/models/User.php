<?php

/**
 * This is the model class for table "wx_user".
 *
 * The followings are the available columns in table 'wx_user':
 * @property integer $id
 * @property string $open_id
 * @property integer $group_id
 * @property string $nickname
 * @property string $tel
 * @property string $email
 * @property integer $sex
 * @property string $name
 * @property string $employee_id
 * @property string $city
 * @property string $province
 * @property string $country
 * @property integer $subscribe_time
 * @property integer $subscribe
 * @property integer $type
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'wx_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('open_id, subscribe_time, subscribe', 'required'),
			array('group_id, sex, subscribe_time, subscribe, type', 'numerical', 'integerOnly'=>true),
			array('open_id, email', 'length', 'max'=>64),
			array('nickname', 'length', 'max'=>36),
			array('tel, city, province', 'length', 'max'=>12),
			array('name, employee_id, country', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, open_id, group_id, nickname, tel, email, sex, name, employee_id, city, province, country, subscribe_time, subscribe, type', 'safe', 'on'=>'search'),
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
			'open_id' => 'Open',
			'group_id' => 'Group',
			'nickname' => 'Nickname',
			'tel' => 'Tel',
			'email' => 'Email',
			'sex' => 'Sex',
			'name' => 'Name',
			'employee_id' => 'Employee',
			'city' => 'City',
			'province' => 'Province',
			'country' => 'Country',
			'subscribe_time' => 'Subscribe Time',
			'subscribe' => 'Subscribe',
			'type' => 'Type',
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
		$criteria->compare('open_id',$this->open_id,true);
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('employee_id',$this->employee_id,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('subscribe_time',$this->subscribe_time);
		$criteria->compare('subscribe',$this->subscribe);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 插入一行数据
     * @param string $openid 用户唯一微信编号
     */
    public function insertOne($openid){

        //判断用户微信id是否存在
        $postid = $this->findByPk($openid);
        if(empty($postid->open_id))
        {
            $ret = new Wxcore(Yii::app()->params['weixin']);
            $usrList = $ret->getUsrinfo($openid);
            $grp = $ret->getUsrgroup($openid);

            //新增记录
            $this->open_id = $openid;
            $this->nickname = $usrList['nickname'];
            $this->sex = $usrList['sex'];
            $this->city = $usrList['city'];
            $this->province = $usrList['province'];
            $this->country = $usrList['country'];

            $this->group_id = $grp['groupid'];
            $this->type = 0;
            $this->subscribe = 1;
            $this->subscribe_time = time();
            return $this->save()?true:false;
        }else
        {
            //更新记录
            $postid->subscribe = 1;
            $postid->subscribe_time = time();
            return $postid->save()?true:false;
        }
    }
    /**
     * 取消关注时
     * @param string $openid 用户唯一微信编号
     */
    public function updateOne($openid){
        //判断用户微信id是否存在
        $postid = $this->findByPk($openid);
        //更新记录
        $postid->subscribe = 0;
        $postid->type = 0;
        return $postid->save()?true:false;

    }
}