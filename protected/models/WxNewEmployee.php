<?php

/**
 * This is the model class for table "wx_new_employee".
 *
 * The followings are the available columns in table 'wx_new_employee':
 * @property integer $id
 * @property string $empty_no
 * @property string $empty_name
 * @property string $employee_name
 * @property string $employee_type
 * @property string $employee_source
 * @property string $employee_degree
 * @property string $employee_brand
 * @property string $hr_market
 * @property string $hr_boss
 * @property string $province
 * @property string $city
 * @property string $stage
 * @property string $tel
 * @property string $email
 * @property string $am_name
 * @property string $am_id
 */
class WxNewEmployee extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WxNewEmployee the static model class
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
		return 'wx_new_employee';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('empty_no', 'length', 'max'=>36),
			array('empty_name', 'length', 'max'=>128),
			array('employee_name, email', 'length', 'max'=>32),
			array('employee_type, employee_source, employee_brand, hr_boss, am_name, am_id', 'length', 'max'=>45),
			array('employee_degree', 'length', 'max'=>8),
			array('hr_market, province, city, stage', 'length', 'max'=>16),
			array('tel', 'length', 'max'=>26),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, empty_no, empty_name, employee_name, employee_type, employee_source, employee_degree, employee_brand, hr_market, hr_boss, province, city, stage, tel, email, am_name, am_id', 'safe', 'on'=>'search'),
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
			'empty_no' => 'Empty No',
			'empty_name' => 'Empty Name',
			'employee_name' => 'Employee Name',
			'employee_type' => 'Employee Type',
			'employee_source' => 'Employee Source',
			'employee_degree' => 'Employee Degree',
			'employee_brand' => 'Employee Brand',
			'hr_market' => 'Hr Market',
			'hr_boss' => 'Hr Boss',
			'province' => 'Province',
			'city' => 'City',
			'stage' => 'Stage',
			'tel' => 'Tel',
			'email' => 'Email',
			'am_name' => 'Am Name',
			'am_id' => 'Am',
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
		$criteria->compare('empty_no',$this->empty_no,true);
		$criteria->compare('empty_name',$this->empty_name,true);
		$criteria->compare('employee_name',$this->employee_name,true);
		$criteria->compare('employee_type',$this->employee_type,true);
		$criteria->compare('employee_source',$this->employee_source,true);
		$criteria->compare('employee_degree',$this->employee_degree,true);
		$criteria->compare('employee_brand',$this->employee_brand,true);
		$criteria->compare('hr_market',$this->hr_market,true);
		$criteria->compare('hr_boss',$this->hr_boss,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('stage',$this->stage,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('am_name',$this->am_name,true);
		$criteria->compare('am_id',$this->am_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 将csv文件保存到数据库中
     *
     * @param string $loadPath csv文件的路径
     * @return int 成功条数
     */
    public function storeCsv($loadPath)
    {

        $connection = Yii::app()->db;
        $sql = sprintf("INSERT INTO %s(`empty_no`, `empty_name`, `employee_name`,
`employee_type`,`employee_source`,`employee_degree`,
`employee_brand`, `hr_market`, `hr_boss`, `province`,
 `city`, `stage`, `tel`, `email`, `am_name`, `am_id`) VALUES",$this->tableName()); //构造SQL

        $this->deleteAll();
        $file_handle = fopen($loadPath, "r");

        $bom = fread($file_handle, 2);
        rewind($file_handle);
        if($bom === chr(0xff).chr(0xfe)  || $bom === chr(0xfe).chr(0xff)){
            $encoding = 'UTF-16';
            stream_filter_append($file_handle,'convert.iconv.'.$encoding.'/UTF-8');
        }
        fgets($file_handle);
        $str = "";
        while (!feof($file_handle)) {
            $strq = trim(fgets($file_handle));
            $arr = explode("\t",$strq);
            $tmpa = trim($arr[0]);

            if(isset($arr[34])&&!empty($tmpa)&&$arr[12]!='"不合格简历"')
            {
                foreach($arr as $k=>$val)
                {
                    $arr[$k] = str_replace(array("'",'"'),"",$val);
                }
                $str .= sprintf("('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),",
                    $arr[0],$arr[1],$arr[2].$arr[3],$arr[4],$arr[5],
                    $arr[6],$arr[7],$arr[8],$arr[9],$arr[10],$arr[11],$arr[12],$arr[31],$arr[32],$arr[33],$arr[34]);
            }

        }
        fclose($file_handle);
        unset($loadPath);
        if(empty($str))
        {
            return "更新数据失败";
        }
        else{
            $sql .= rtrim($str,",");
            $sqlCom = $connection->createCommand($sql)->execute();
            Homeconf::model()->updateByPk("csv",array('value'=>date('Y-m-d')));
            return  "添加数据成功";
        }
    }
}