<?php

// app/Services/GraphQLClient.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GraphQLClient
{
    protected $endpoint = 'https://y.lydt.work/graphql';

    /**
     * 执行 GraphQL 查询
     */
    public function query($query, $variables = [])
    {
        try {
            $response = Http::timeout(30)
                ->post($this->endpoint, [
                    'query' => $query,
                    'variables' => $variables,
                ]);

            if ($response->failed()) {
                return ['error' => 'GraphQL request failed', 'status' => $response->status()];
            }

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * 获取分类列表
     */
    public function getCategories()
    {
        $query = <<<'GQL'
            {
              data:tags_by_type(withType:"ly"){
                id
                name
                type
                order_column
                programs:ly_metas{
                  id
                  name
                  alias:code
                  avatar:cover
                  description
                  begin_at
                  end_at
                }
              }
            }
        GQL;

        $result = $this->query($query);
        
        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }

        // 移除最后一个项目（粤语节目）
        $data = $result['data']['data'] ?? [];
        if (!empty($data)) {
            array_pop($data);
        }

        return $data;
    }

    /**
     * 获取所有节目
     */
    public function getPrograms()
    {
        $query = <<<'GQL'
            {
              data:ly_metas{
                id
                name
                avatar:cover
                category
                alias:code
                begin_at
                end_at
                description
                announcers{
                    id
                    name
                    avatar
                    birthday
                    description
                    begin_at
                    stop_at
                }
              }
            }
        GQL;

        $result = $this->query($query);
        
        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }

        // return $result['data']['data'] ?? [];
        $data = $result['data']['data'] ?? [];

        // 过滤掉 end_at 不等于 null 的记录
        $filtered = array_filter($data, function($item) {
            return $item['end_at'] === null;
        });

        return $filtered;
    }

    /**
     * 获取今天的项目
     */
    public function getTodayItems()
    {
        $now = date('Y-m-d');

        // 查询 ly_items
        $query1 = <<<GQL
            {
              ly_items(play_at: "$now 00:00:00") {
                data {
                  id
                  description
                  alias
                  play_at
                  path: novaMp3Path
                  link: path
                  program: ly_meta {
                    id
                    name
                    code
                  }
                }
              }
            }
        GQL;

        $result1 = $this->query($query1);
        $data1 = $result1['data']['ly_items']['data'] ?? [];

        // 查询 lts_items
        $query2 = <<<GQL
            {
              lts_items(play_at: "$now 00:00:00") {
                data {
                  id
                  description
                  alias
                  play_at
                  path: novaMp3Path
                  link: path
                  program: ly_meta {
                    id
                    name
                    code
                  }
                }
              }
            }
        GQL;

        $result2 = $this->query($query2);
        $data2 = $result2['data']['lts_items']['data'] ?? [];

        // 合并并随机排序
        $merged = array_merge($data1, $data2);
        shuffle($merged);

        return $merged;
    }

    /**
     * 获取单个节目详情
     */
    public function getProgramByCode($code)
    {
        $isLts = Str::startsWith($code, 'lts');
        $hasManyType = $isLts?"ltsItems":"lyItems";
        $programType = $isLts?"ly_meta":"ly_meta";
        $count = $isLts?30:365;
        $query = <<<GQL
            {
              data:ly_meta_by_code(code: "$code") {
                id
                name
                code
                cover
                description
                begin_at
                end_at
                remark
                category
                ly_items: $hasManyType (first:$count) {
                  data {
                    id
                    alias
                    description
                    play_at
                    path: novaMp3Path
                    link: path
                    program: $programType {
                      id
                      name
                      code
                    }
                  }
                  paginatorInfo {
                    total
                    currentPage
                    hasMorePages
                  }
                }
              }
            }
        GQL;

        $result = $this->query($query);

        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }
        // 修改：如果是lts，则过滤30天内的内容
        if($isLts) {
            $items = $result['data']['data']['ly_items']['data'] ?? [];
            $thirtyDaysAgo = now()->subDays(30);
            
            // 过滤出30天内的内容
            $filteredItems = array_filter($items, function($item) use ($thirtyDaysAgo) {
                // play_at 格式: "2025-11-25 00:00:00"
                $playAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item['play_at']);
                return $playAt->greaterThanOrEqualTo($thirtyDaysAgo);
            });
            
            // 更新结果中的items和total计数
            $result['data']['data']['ly_items']['data'] = array_values($filteredItems);
            $result['data']['data']['ly_items']['paginatorInfo']['total'] = count($filteredItems);
        }
        return $result['data']['data'] ?? null;
    }
}
