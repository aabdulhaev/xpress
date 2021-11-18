<?php
namespace common\forms;

use common\access\Rbac;
use common\models\Client;
use yii\base\Model;
use common\models\User;

/**
 * @OA\Schema()
 *
 * @OA\Property(property="status", type="int", enum={0,5,10})
 */
class StatusUpdateForm extends BaseUserUpdateForm
{

}
