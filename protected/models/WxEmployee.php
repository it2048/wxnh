<?php

/**
 * This is the model class for table "wx_employee".
 *
 * The followings are the available columns in table 'wx_employee':
 * @property string $emp_id
 * @property string $emp_name
 * @property string $dep_id
 * @property string $dep_name
 * @property string $add
 * @property string $degree
 * @property string $emp_type
 * @property string $cid
 * @property integer $sex
 * @property string $company
 * @property string $tel
 * @property string $email
 */
class WxEmployee extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WxEmployee the static model class
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
		return 'wx_employee';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('emp_id', 'required'),
			array('sex', 'numerical', 'integerOnly'=>true),
			array('emp_id, emp_name, dep_id, degree, cid, email', 'length', 'max'=>64),
			array('dep_name, add', 'length', 'max'=>128),
			array('emp_type, company, tel', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('emp_id, emp_name, dep_id, dep_name, add, degree, emp_type, cid, sex, company, tel, email', 'safe', 'on'=>'search'),
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
			'emp_id' => 'Emp',
			'emp_name' => 'Emp Name',
			'dep_id' => 'Dep',
			'dep_name' => 'Dep Name',
			'add' => 'Add',
			'degree' => 'Degree',
			'emp_type' => 'Emp Type',
			'cid' => 'Cid',
			'sex' => 'Sex',
			'company' => 'Company',
			'tel' => 'Tel',
			'email' => 'Email',
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

		$criteria->compare('emp_id',$this->emp_id,true);
		$criteria->compare('emp_name',$this->emp_name,true);
		$criteria->compare('dep_id',$this->dep_id,true);
		$criteria->compare('dep_name',$this->dep_name,true);
		$criteria->compare('add',$this->add,true);
		$criteria->compare('degree',$this->degree,true);
		$criteria->compare('emp_type',$this->emp_type,true);
		$criteria->compare('cid',$this->cid,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('email',$this->email,true);

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
        $this->deleteAll();
        $file_handle = fopen($loadPath, "r");
        fgets($file_handle);
        $str = "";
        while (!feof($file_handle)) {
            $line = fgets($file_handle);
            if(trim($line)!="")
            {
                $arr = explode(",",$line);
                $tmp = new WxEmployee();
                if(isset($arr[9])&&!empty($arr[0]))
                {
                    foreach($arr as $k=>$val)
                    {
                        $arr[$k] = trim(iconv("GBK","UTF-8//IGNORE", $val));
                    }

                    $tmp->emp_id = $arr[0];
                    $tmp->emp_name = $arr[1];
                    $tmp->dep_id = $arr[2];
                    $tmp->dep_name = $arr[3];
                    $tmp->company = $arr[4];
                    $tmp->add = $arr[5];
                    $tmp->degree = $arr[6];
                    $tmp->emp_type = $arr[7];
                    $tmp->cid = $arr[8];
                    $tmp->sex = $arr[9]=='男'?1:2;
                    $tmp->tel = empty($arr[10])?"":$arr[10];
                    $tmp->email = empty($arr[11])?"":$arr[11];
                    if(!$tmp->save())
                    {
                        $str .= $arr[0].",";
                    }
                }
            }
        }
        fclose($file_handle);
        return  empty($str)?"全部更新成功":$str."更新失败";
    }
}