<?php
namespace app\models\template;

use Yii;
use app\models\Template;

class UpdateTemplate extends Template
{
    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['id', 'post_method'], 'required'],
            [['header_fields', 'request_fields', 'response_fields'], 'string'],
        ];
    }

    /**
     * 保存模板
     * @return bool
     */
    public function store()
    {
        if(!$this->validate()){
            return false;
        }

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $template = &$this;

        if(strlen($this->response_fields) == 0){
            $this->addError($template->getErrorLabel(), '响应参数不能为空');
            $transaction->rollBack();
            return false;
        }

        $template->post_method     = $this->post_method;
        $template->header_fields   = $this->header_fields;
        $template->request_fields  = $this->request_fields;
        $template->response_fields = $this->response_fields;
        $template->status     = $template::ACTIVE_STATUS;
        $template->updater_id = Yii::$app->user->identity->id;

        if(!$template->save()){
            $this->addError($template->getErrorLabel(), $template->getErrorMessage());
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
