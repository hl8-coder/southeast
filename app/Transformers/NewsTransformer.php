<?php
namespace App\Transformers;

use App\Models\Model;
use App\Models\News;

/**
 * @OA\Schema(
 *   schema="News",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="新闻id"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="title", type="string", description="标题"),
 *   @OA\Property(property="content", type="string", description="内容"),
 *   @OA\Property(property="sort", type="string", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class NewsTransformer extends Transformer
{
    public function transform(News $news)
    {
        $data = [
            'id'            => $news->id,
            'currency'      => $news->currency,
            'title'         => $news->title,
            'content'       => $news->content,
            'sort'          => $news->sort,
            'status'        => transfer_show_value($news->status, Model::$booleanStatusesDropList),
            'created_at'    => convert_time($news->created_at),
        ];

        switch ($this->type) {
            default:
                return $data;

            case 'index':
                return collect($data)->except(['content'])->toArray();
        }
    }
}