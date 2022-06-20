<?php

namespace Demo\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Poppy\System\Models\PamAccount;

/**
 * \Poppy\PoppyCoreDemo
 *
 * @property int $is_open 是否开启
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $style
 * @property int $uid
 * @property int $sort
 * @property int|null $status
 * @property string|null $desc
 * @property string|null $email
 * @property string|null $title
 * @property int $list_order
 * @property string|null $username
 * @property string|null $file
 * @property string|null $last_name
 * @property string|null $link
 * @property string|null $image
 * @property int|null $progress
 * @property int|null $trashed
 * @property string|null $content
 * @property string|null $b
 * @property int|null $account_id
 * @property string|null $loading
 * @property string|null $first_name
 * @property string|null $date
 * @property string|null $day
 * @property string|null $year
 * @property string|null $month
 * @property int|null $is_enable
 * @property string|null $op_group
 * @property string|null $type
 * @property string|null $handle
 * @property string|null $order_able
 * @property string|null $modal
 * @property string|null $prefix
 * @property string|null $suffix
 * @property-read PamAccount|null $pam
 * @method static Builder|DemoWebappNpk newModelQuery()
 * @method static Builder|DemoWebappNpk newQuery()
 * @method static Builder|DemoWebappNpk query()
 * @mixin Eloquent
 */
class DemoWebappNpk extends Model
{
    // change tablename
    public $timestamps = false;

    protected $primaryKey = '';

    protected $table = 'demo_webapp_npk';

    protected $fillable = [
        // fillable
    ];

    protected $casts = [
        'setting',
    ];

    public function user()
    {
        return $this->hasOne(DemoUser::class, 'id', 'account_id');
    }

    public function comment()
    {
        return $this->hasMany(DemoComment::class, 'webapp_id', 'id');
    }


    public static function kvStatus($key = null)
    {
        $defs = [
            1 => '未发布',
            2 => '草稿',
            5 => '待审核',
            3 => '已发布',
            4 => '已删除',
        ];
        return kv($defs, $key);
    }

}