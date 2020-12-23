<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\City::query()->truncate();
        $countries = \App\Models\Country::query()->get();
        foreach ($countries as $country) {
            switch ($country->id) {
                case 1:
                    $cities = [
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "东城",
                            "city_code"  => "1",
                            "remark"     => "北京 东城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "西城",
                            "city_code"  => "2",
                            "remark"     => "北京 西城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "朝阳",
                            "city_code"  => "5",
                            "remark"     => "北京 朝阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "丰台",
                            "city_code"  => "6",
                            "remark"     => "北京 丰台"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "石景山",
                            "city_code"  => "7",
                            "remark"     => "北京 石景山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "海淀",
                            "city_code"  => "8",
                            "remark"     => "北京 海淀"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "门头沟",
                            "city_code"  => "9",
                            "remark"     => "北京 门头沟"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "房山",
                            "city_code"  => "11",
                            "remark"     => "北京 房山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "通州",
                            "city_code"  => "12",
                            "remark"     => "北京 通州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "顺义",
                            "city_code"  => "13",
                            "remark"     => "北京 顺义"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "昌平",
                            "city_code"  => "21",
                            "remark"     => "北京 昌平"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大兴",
                            "city_code"  => "24",
                            "remark"     => "北京 大兴"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "平谷",
                            "city_code"  => "26",
                            "remark"     => "北京 平谷"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "怀柔",
                            "city_code"  => "27",
                            "remark"     => "北京 怀柔"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "密云",
                            "city_code"  => "28",
                            "remark"     => "北京 密云"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "延庆",
                            "city_code"  => "29",
                            "remark"     => "北京 延庆"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "和平",
                            "city_code"  => "1",
                            "remark"     => "天津 和平"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "河东",
                            "city_code"  => "2",
                            "remark"     => "天津 河东"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "河西",
                            "city_code"  => "3",
                            "remark"     => "天津 河西"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南开",
                            "city_code"  => "4",
                            "remark"     => "天津 南开"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "河北",
                            "city_code"  => "5",
                            "remark"     => "天津 河北"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "红桥",
                            "city_code"  => "6",
                            "remark"     => "天津 红桥"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "滨海新区",
                            "city_code"  => "26",
                            "remark"     => "天津 滨海新区"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "东丽",
                            "city_code"  => "10",
                            "remark"     => "天津 东丽"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "西青",
                            "city_code"  => "11",
                            "remark"     => "天津 西青"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "津南",
                            "city_code"  => "12",
                            "remark"     => "天津 津南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "北辰",
                            "city_code"  => "13",
                            "remark"     => "天津 北辰"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宁河",
                            "city_code"  => "21",
                            "remark"     => "天津 宁河"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "武清",
                            "city_code"  => "22",
                            "remark"     => "天津 武清"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "静海",
                            "city_code"  => "23",
                            "remark"     => "天津 静海"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宝坻",
                            "city_code"  => "24",
                            "remark"     => "天津 宝坻"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "蓟县",
                            "city_code"  => "25",
                            "remark"     => "天津 蓟县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "石家庄",
                            "city_code"  => "1",
                            "remark"     => "河北 石家庄"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "唐山",
                            "city_code"  => "2",
                            "remark"     => "河北 唐山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "秦皇岛",
                            "city_code"  => "3",
                            "remark"     => "河北 秦皇岛"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "邯郸",
                            "city_code"  => "4",
                            "remark"     => "河北 邯郸"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "邢台",
                            "city_code"  => "5",
                            "remark"     => "河北 邢台"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "保定",
                            "city_code"  => "6",
                            "remark"     => "河北 保定"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "张家口",
                            "city_code"  => "7",
                            "remark"     => "河北 张家口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "承德",
                            "city_code"  => "8",
                            "remark"     => "河北 承德"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "沧州",
                            "city_code"  => "9",
                            "remark"     => "河北 沧州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "廊坊",
                            "city_code"  => "10",
                            "remark"     => "河北 廊坊"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "衡水",
                            "city_code"  => "11",
                            "remark"     => "河北 衡水"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "太原",
                            "city_code"  => "1",
                            "remark"     => "山西 太原"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大同",
                            "city_code"  => "2",
                            "remark"     => "山西 大同"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阳泉",
                            "city_code"  => "3",
                            "remark"     => "山西 阳泉"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "长治",
                            "city_code"  => "4",
                            "remark"     => "山西 长治"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "晋城",
                            "city_code"  => "5",
                            "remark"     => "山西 晋城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "朔州",
                            "city_code"  => "6",
                            "remark"     => "山西 朔州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "晋中",
                            "city_code"  => "7",
                            "remark"     => "山西 晋中"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "运城",
                            "city_code"  => "8",
                            "remark"     => "山西 运城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "忻州",
                            "city_code"  => "9",
                            "remark"     => "山西 忻州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "临汾",
                            "city_code"  => "10",
                            "remark"     => "山西 临汾"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "吕梁",
                            "city_code"  => "11",
                            "remark"     => "山西 吕梁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "呼和浩特",
                            "city_code"  => "1",
                            "remark"     => "内蒙古 呼和浩特"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "包头",
                            "city_code"  => "2",
                            "remark"     => "内蒙古 包头"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "乌海",
                            "city_code"  => "3",
                            "remark"     => "内蒙古 乌海"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "赤峰",
                            "city_code"  => "4",
                            "remark"     => "内蒙古 赤峰"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "通辽",
                            "city_code"  => "5",
                            "remark"     => "内蒙古 通辽"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鄂尔多斯",
                            "city_code"  => "6",
                            "remark"     => "内蒙古 鄂尔多斯"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "呼伦贝尔",
                            "city_code"  => "7",
                            "remark"     => "内蒙古 呼伦贝尔"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "巴彦淖尔",
                            "city_code"  => "8",
                            "remark"     => "内蒙古 巴彦淖尔"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "乌兰察布",
                            "city_code"  => "9",
                            "remark"     => "内蒙古 乌兰察布"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "兴安",
                            "city_code"  => "22",
                            "remark"     => "内蒙古 兴安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "锡林郭勒",
                            "city_code"  => "25",
                            "remark"     => "内蒙古 锡林郭勒"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阿拉善",
                            "city_code"  => "29",
                            "remark"     => "内蒙古 阿拉善"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "沈阳",
                            "city_code"  => "1",
                            "remark"     => "辽宁 沈阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大连",
                            "city_code"  => "2",
                            "remark"     => "辽宁 大连"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鞍山",
                            "city_code"  => "3",
                            "remark"     => "辽宁 鞍山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "抚顺",
                            "city_code"  => "4",
                            "remark"     => "辽宁 抚顺"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "本溪",
                            "city_code"  => "5",
                            "remark"     => "辽宁 本溪"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "丹东",
                            "city_code"  => "6",
                            "remark"     => "辽宁 丹东"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "锦州",
                            "city_code"  => "7",
                            "remark"     => "辽宁 锦州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "营口",
                            "city_code"  => "8",
                            "remark"     => "辽宁 营口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阜新",
                            "city_code"  => "9",
                            "remark"     => "辽宁 阜新"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "辽阳",
                            "city_code"  => "10",
                            "remark"     => "辽宁 辽阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "盘锦",
                            "city_code"  => "11",
                            "remark"     => "辽宁 盘锦"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "铁岭",
                            "city_code"  => "12",
                            "remark"     => "辽宁 铁岭"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "朝阳",
                            "city_code"  => "13",
                            "remark"     => "辽宁 朝阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "葫芦岛",
                            "city_code"  => "14",
                            "remark"     => "辽宁 葫芦岛"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "长春",
                            "city_code"  => "1",
                            "remark"     => "吉林 长春"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "吉林",
                            "city_code"  => "2",
                            "remark"     => "吉林 吉林"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "四平",
                            "city_code"  => "3",
                            "remark"     => "吉林 四平"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "辽源",
                            "city_code"  => "4",
                            "remark"     => "吉林 辽源"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "通化",
                            "city_code"  => "5",
                            "remark"     => "吉林 通化"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "白山",
                            "city_code"  => "6",
                            "remark"     => "吉林 白山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "松原",
                            "city_code"  => "7",
                            "remark"     => "吉林 松原"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "白城",
                            "city_code"  => "8",
                            "remark"     => "吉林 白城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "延边",
                            "city_code"  => "24",
                            "remark"     => "吉林 延边"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "哈尔滨",
                            "city_code"  => "1",
                            "remark"     => "黑龙江 哈尔滨"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "齐齐哈尔",
                            "city_code"  => "2",
                            "remark"     => "黑龙江 齐齐哈尔"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鸡西",
                            "city_code"  => "3",
                            "remark"     => "黑龙江 鸡西"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鹤岗",
                            "city_code"  => "4",
                            "remark"     => "黑龙江 鹤岗"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "双鸭山",
                            "city_code"  => "5",
                            "remark"     => "黑龙江 双鸭山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大庆",
                            "city_code"  => "6",
                            "remark"     => "黑龙江 大庆"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "伊春",
                            "city_code"  => "7",
                            "remark"     => "黑龙江 伊春"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "佳木斯",
                            "city_code"  => "8",
                            "remark"     => "黑龙江 佳木斯"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "七台河",
                            "city_code"  => "9",
                            "remark"     => "黑龙江 七台河"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "牡丹江",
                            "city_code"  => "10",
                            "remark"     => "黑龙江 牡丹江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黑河",
                            "city_code"  => "11",
                            "remark"     => "黑龙江 黑河"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "绥化",
                            "city_code"  => "12",
                            "remark"     => "黑龙江 绥化"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大兴安岭",
                            "city_code"  => "27",
                            "remark"     => "黑龙江 大兴安岭"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黄浦",
                            "city_code"  => "1",
                            "remark"     => "上海 黄浦"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "卢湾",
                            "city_code"  => "3",
                            "remark"     => "上海 卢湾"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "徐汇",
                            "city_code"  => "4",
                            "remark"     => "上海 徐汇"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "长宁",
                            "city_code"  => "5",
                            "remark"     => "上海 长宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "静安",
                            "city_code"  => "6",
                            "remark"     => "上海 静安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "普陀",
                            "city_code"  => "7",
                            "remark"     => "上海 普陀"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "闸北",
                            "city_code"  => "8",
                            "remark"     => "上海 闸北"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "虹口",
                            "city_code"  => "9",
                            "remark"     => "上海 虹口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "杨浦",
                            "city_code"  => "11",
                            "remark"     => "上海 杨浦"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "闵行",
                            "city_code"  => "12",
                            "remark"     => "上海 闵行"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宝山",
                            "city_code"  => "13",
                            "remark"     => "上海 宝山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "嘉定",
                            "city_code"  => "14",
                            "remark"     => "上海 嘉定"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "浦东新区",
                            "city_code"  => "15",
                            "remark"     => "上海 浦东新区"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "金山",
                            "city_code"  => "16",
                            "remark"     => "上海 金山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "松江",
                            "city_code"  => "17",
                            "remark"     => "上海 松江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "奉贤",
                            "city_code"  => "26",
                            "remark"     => "上海 奉贤"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "青浦",
                            "city_code"  => "29",
                            "remark"     => "上海 青浦"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "崇明",
                            "city_code"  => "30",
                            "remark"     => "上海 崇明"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南京",
                            "city_code"  => "1",
                            "remark"     => "江苏 南京"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "无锡",
                            "city_code"  => "2",
                            "remark"     => "江苏 无锡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "徐州",
                            "city_code"  => "3",
                            "remark"     => "江苏 徐州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "常州",
                            "city_code"  => "4",
                            "remark"     => "江苏 常州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "苏州",
                            "city_code"  => "5",
                            "remark"     => "江苏 苏州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南通",
                            "city_code"  => "6",
                            "remark"     => "江苏 南通"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "连云港",
                            "city_code"  => "7",
                            "remark"     => "江苏 连云港"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "淮安",
                            "city_code"  => "8",
                            "remark"     => "江苏 淮安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "盐城",
                            "city_code"  => "9",
                            "remark"     => "江苏 盐城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "扬州",
                            "city_code"  => "10",
                            "remark"     => "江苏 扬州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "镇江",
                            "city_code"  => "11",
                            "remark"     => "江苏 镇江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "泰州",
                            "city_code"  => "12",
                            "remark"     => "江苏 泰州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宿迁",
                            "city_code"  => "13",
                            "remark"     => "江苏 宿迁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "杭州",
                            "city_code"  => "1",
                            "remark"     => "浙江 杭州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宁波",
                            "city_code"  => "2",
                            "remark"     => "浙江 宁波"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "温州",
                            "city_code"  => "3",
                            "remark"     => "浙江 温州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "嘉兴",
                            "city_code"  => "4",
                            "remark"     => "浙江 嘉兴"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "湖州",
                            "city_code"  => "5",
                            "remark"     => "浙江 湖州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "绍兴",
                            "city_code"  => "6",
                            "remark"     => "浙江 绍兴"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "金华",
                            "city_code"  => "7",
                            "remark"     => "浙江 金华"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "衢州",
                            "city_code"  => "8",
                            "remark"     => "浙江 衢州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "舟山",
                            "city_code"  => "9",
                            "remark"     => "浙江 舟山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台州",
                            "city_code"  => "10",
                            "remark"     => "浙江 台州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "丽水",
                            "city_code"  => "11",
                            "remark"     => "浙江 丽水"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "合肥",
                            "city_code"  => "1",
                            "remark"     => "安徽 合肥"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "芜湖",
                            "city_code"  => "2",
                            "remark"     => "安徽 芜湖"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "蚌埠",
                            "city_code"  => "3",
                            "remark"     => "安徽 蚌埠"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "淮南",
                            "city_code"  => "4",
                            "remark"     => "安徽 淮南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "马鞍山",
                            "city_code"  => "5",
                            "remark"     => "安徽 马鞍山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "淮北",
                            "city_code"  => "6",
                            "remark"     => "安徽 淮北"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "铜陵",
                            "city_code"  => "7",
                            "remark"     => "安徽 铜陵"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "安庆",
                            "city_code"  => "8",
                            "remark"     => "安徽 安庆"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黄山",
                            "city_code"  => "10",
                            "remark"     => "安徽 黄山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "滁州",
                            "city_code"  => "11",
                            "remark"     => "安徽 滁州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阜阳",
                            "city_code"  => "12",
                            "remark"     => "安徽 阜阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宿州",
                            "city_code"  => "13",
                            "remark"     => "安徽 宿州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "六安",
                            "city_code"  => "15",
                            "remark"     => "安徽 六安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "亳州",
                            "city_code"  => "16",
                            "remark"     => "安徽 亳州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "池州",
                            "city_code"  => "17",
                            "remark"     => "安徽 池州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宣城",
                            "city_code"  => "18",
                            "remark"     => "安徽 宣城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "福州",
                            "city_code"  => "1",
                            "remark"     => "福建 福州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "厦门",
                            "city_code"  => "2",
                            "remark"     => "福建 厦门"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "莆田",
                            "city_code"  => "3",
                            "remark"     => "福建 莆田"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "三明",
                            "city_code"  => "4",
                            "remark"     => "福建 三明"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "泉州",
                            "city_code"  => "5",
                            "remark"     => "福建 泉州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "漳州",
                            "city_code"  => "6",
                            "remark"     => "福建 漳州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南平",
                            "city_code"  => "7",
                            "remark"     => "福建 南平"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "龙岩",
                            "city_code"  => "8",
                            "remark"     => "福建 龙岩"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宁德",
                            "city_code"  => "9",
                            "remark"     => "福建 宁德"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南昌",
                            "city_code"  => "1",
                            "remark"     => "江西 南昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "景德镇",
                            "city_code"  => "2",
                            "remark"     => "江西 景德镇"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "萍乡",
                            "city_code"  => "3",
                            "remark"     => "江西 萍乡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "九江",
                            "city_code"  => "4",
                            "remark"     => "江西 九江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "新余",
                            "city_code"  => "5",
                            "remark"     => "江西 新余"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鹰潭",
                            "city_code"  => "6",
                            "remark"     => "江西 鹰潭"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "赣州",
                            "city_code"  => "7",
                            "remark"     => "江西 赣州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "吉安",
                            "city_code"  => "8",
                            "remark"     => "江西 吉安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宜春",
                            "city_code"  => "9",
                            "remark"     => "江西 宜春"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "抚州",
                            "city_code"  => "10",
                            "remark"     => "江西 抚州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "上饶",
                            "city_code"  => "11",
                            "remark"     => "江西 上饶"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "济南",
                            "city_code"  => "1",
                            "remark"     => "山东 济南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "青岛",
                            "city_code"  => "2",
                            "remark"     => "山东 青岛"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "淄博",
                            "city_code"  => "3",
                            "remark"     => "山东 淄博"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "枣庄",
                            "city_code"  => "4",
                            "remark"     => "山东 枣庄"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "东营",
                            "city_code"  => "5",
                            "remark"     => "山东 东营"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "烟台",
                            "city_code"  => "6",
                            "remark"     => "山东 烟台"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "潍坊",
                            "city_code"  => "7",
                            "remark"     => "山东 潍坊"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "济宁",
                            "city_code"  => "8",
                            "remark"     => "山东 济宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "泰安",
                            "city_code"  => "9",
                            "remark"     => "山东 泰安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "威海",
                            "city_code"  => "10",
                            "remark"     => "山东 威海"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "日照",
                            "city_code"  => "11",
                            "remark"     => "山东 日照"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "莱芜",
                            "city_code"  => "12",
                            "remark"     => "山东 莱芜"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "临沂",
                            "city_code"  => "13",
                            "remark"     => "山东 临沂"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "德州",
                            "city_code"  => "14",
                            "remark"     => "山东 德州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "聊城",
                            "city_code"  => "15",
                            "remark"     => "山东 聊城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "滨州",
                            "city_code"  => "16",
                            "remark"     => "山东 滨州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "菏泽",
                            "city_code"  => "17",
                            "remark"     => "山东 菏泽"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "郑州",
                            "city_code"  => "1",
                            "remark"     => "河南 郑州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "开封",
                            "city_code"  => "2",
                            "remark"     => "河南 开封"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "洛阳",
                            "city_code"  => "3",
                            "remark"     => "河南 洛阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "平顶山",
                            "city_code"  => "4",
                            "remark"     => "河南 平顶山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "安阳",
                            "city_code"  => "5",
                            "remark"     => "河南 安阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鹤壁",
                            "city_code"  => "6",
                            "remark"     => "河南 鹤壁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "新乡",
                            "city_code"  => "7",
                            "remark"     => "河南 新乡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "焦作",
                            "city_code"  => "8",
                            "remark"     => "河南 焦作"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "濮阳",
                            "city_code"  => "9",
                            "remark"     => "河南 濮阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "许昌",
                            "city_code"  => "10",
                            "remark"     => "河南 许昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "漯河",
                            "city_code"  => "11",
                            "remark"     => "河南 漯河"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "三门峡",
                            "city_code"  => "12",
                            "remark"     => "河南 三门峡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南阳",
                            "city_code"  => "13",
                            "remark"     => "河南 南阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "商丘",
                            "city_code"  => "14",
                            "remark"     => "河南 商丘"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "信阳",
                            "city_code"  => "15",
                            "remark"     => "河南 信阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "周口",
                            "city_code"  => "16",
                            "remark"     => "河南 周口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "驻马店",
                            "city_code"  => "17",
                            "remark"     => "河南 驻马店"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "济源",
                            "city_code"  => "18",
                            "remark"     => "河南 济源"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "武汉",
                            "city_code"  => "1",
                            "remark"     => "湖北 武汉"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黄石",
                            "city_code"  => "2",
                            "remark"     => "湖北 黄石"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "十堰",
                            "city_code"  => "3",
                            "remark"     => "湖北 十堰"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宜昌",
                            "city_code"  => "5",
                            "remark"     => "湖北 宜昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "襄阳",
                            "city_code"  => "6",
                            "remark"     => "湖北 襄阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "鄂州",
                            "city_code"  => "7",
                            "remark"     => "湖北 鄂州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "荆门",
                            "city_code"  => "8",
                            "remark"     => "湖北 荆门"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "孝感",
                            "city_code"  => "9",
                            "remark"     => "湖北 孝感"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "荆州",
                            "city_code"  => "10",
                            "remark"     => "湖北 荆州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黄冈",
                            "city_code"  => "11",
                            "remark"     => "湖北 黄冈"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "咸宁",
                            "city_code"  => "12",
                            "remark"     => "湖北 咸宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "随州",
                            "city_code"  => "13",
                            "remark"     => "湖北 随州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "恩施",
                            "city_code"  => "28",
                            "remark"     => "湖北 恩施"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "仙桃",
                            "city_code"  => "94",
                            "remark"     => "湖北 仙桃"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "潜江",
                            "city_code"  => "95",
                            "remark"     => "湖北 潜江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "天门",
                            "city_code"  => "96",
                            "remark"     => "湖北 天门"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "神农架",
                            "city_code"  => "A21",
                            "remark"     => "湖北 神农架"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "长沙",
                            "city_code"  => "1",
                            "remark"     => "湖南 长沙"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "株洲",
                            "city_code"  => "2",
                            "remark"     => "湖南 株洲"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "湘潭",
                            "city_code"  => "3",
                            "remark"     => "湖南 湘潭"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "衡阳",
                            "city_code"  => "4",
                            "remark"     => "湖南 衡阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "邵阳",
                            "city_code"  => "5",
                            "remark"     => "湖南 邵阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "岳阳",
                            "city_code"  => "6",
                            "remark"     => "湖南 岳阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "常德",
                            "city_code"  => "7",
                            "remark"     => "湖南 常德"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "张家界",
                            "city_code"  => "8",
                            "remark"     => "湖南 张家界"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "益阳",
                            "city_code"  => "9",
                            "remark"     => "湖南 益阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "郴州",
                            "city_code"  => "10",
                            "remark"     => "湖南 郴州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "永州",
                            "city_code"  => "11",
                            "remark"     => "湖南 永州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "怀化",
                            "city_code"  => "12",
                            "remark"     => "湖南 怀化"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "娄底",
                            "city_code"  => "13",
                            "remark"     => "湖南 娄底"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "湘西",
                            "city_code"  => "31",
                            "remark"     => "湖南 湘西"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "广州",
                            "city_code"  => "1",
                            "remark"     => "广东 广州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "韶关",
                            "city_code"  => "2",
                            "remark"     => "广东 韶关"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "深圳",
                            "city_code"  => "3",
                            "remark"     => "广东 深圳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "珠海",
                            "city_code"  => "4",
                            "remark"     => "广东 珠海"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "汕头",
                            "city_code"  => "5",
                            "remark"     => "广东 汕头"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "佛山",
                            "city_code"  => "6",
                            "remark"     => "广东 佛山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "江门",
                            "city_code"  => "7",
                            "remark"     => "广东 江门"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "湛江",
                            "city_code"  => "8",
                            "remark"     => "广东 湛江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "茂名",
                            "city_code"  => "9",
                            "remark"     => "广东 茂名"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "肇庆",
                            "city_code"  => "12",
                            "remark"     => "广东 肇庆"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "惠州",
                            "city_code"  => "13",
                            "remark"     => "广东 惠州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "梅州",
                            "city_code"  => "14",
                            "remark"     => "广东 梅州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "汕尾",
                            "city_code"  => "15",
                            "remark"     => "广东 汕尾"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "河源",
                            "city_code"  => "16",
                            "remark"     => "广东 河源"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阳江",
                            "city_code"  => "17",
                            "remark"     => "广东 阳江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "清远",
                            "city_code"  => "18",
                            "remark"     => "广东 清远"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "东莞",
                            "city_code"  => "19",
                            "remark"     => "广东 东莞"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "中山",
                            "city_code"  => "20",
                            "remark"     => "广东 中山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "潮州",
                            "city_code"  => "51",
                            "remark"     => "广东 潮州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "揭阳",
                            "city_code"  => "52",
                            "remark"     => "广东 揭阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "云浮",
                            "city_code"  => "53",
                            "remark"     => "广东 云浮"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南宁",
                            "city_code"  => "1",
                            "remark"     => "广西 南宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "柳州",
                            "city_code"  => "2",
                            "remark"     => "广西 柳州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "桂林",
                            "city_code"  => "3",
                            "remark"     => "广西 桂林"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "梧州",
                            "city_code"  => "4",
                            "remark"     => "广西 梧州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "北海",
                            "city_code"  => "5",
                            "remark"     => "广西 北海"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "防城港",
                            "city_code"  => "6",
                            "remark"     => "广西 防城港"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "钦州",
                            "city_code"  => "7",
                            "remark"     => "广西 钦州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "贵港",
                            "city_code"  => "8",
                            "remark"     => "广西 贵港"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "玉林",
                            "city_code"  => "9",
                            "remark"     => "广西 玉林"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "百色",
                            "city_code"  => "10",
                            "remark"     => "广西 百色"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "贺州",
                            "city_code"  => "11",
                            "remark"     => "广西 贺州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "河池",
                            "city_code"  => "12",
                            "remark"     => "广西 河池"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "来宾",
                            "city_code"  => "13",
                            "remark"     => "广西 来宾"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "崇左",
                            "city_code"  => "14",
                            "remark"     => "广西 崇左"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "海口",
                            "city_code"  => "1",
                            "remark"     => "海南 海口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "三亚",
                            "city_code"  => "2",
                            "remark"     => "海南 三亚"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "三沙",
                            "city_code"  => "3",
                            "remark"     => "海南 三沙"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "五指山",
                            "city_code"  => "91",
                            "remark"     => "海南 五指山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "琼海",
                            "city_code"  => "92",
                            "remark"     => "海南 琼海"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "儋州",
                            "city_code"  => "93",
                            "remark"     => "海南 儋州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "文昌",
                            "city_code"  => "95",
                            "remark"     => "海南 文昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "万宁",
                            "city_code"  => "96",
                            "remark"     => "海南 万宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "东方",
                            "city_code"  => "97",
                            "remark"     => "海南 东方"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "定安",
                            "city_code"  => "A25",
                            "remark"     => "海南 定安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "屯昌",
                            "city_code"  => "A26",
                            "remark"     => "海南 屯昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "澄迈",
                            "city_code"  => "A27",
                            "remark"     => "海南 澄迈"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "临高",
                            "city_code"  => "A28",
                            "remark"     => "海南 临高"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "白沙",
                            "city_code"  => "A30",
                            "remark"     => "海南 白沙"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "昌江",
                            "city_code"  => "A31",
                            "remark"     => "海南 昌江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "乐东",
                            "city_code"  => "A33",
                            "remark"     => "海南 乐东"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "陵水",
                            "city_code"  => "A34",
                            "remark"     => "海南 陵水"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "保亭",
                            "city_code"  => "A35",
                            "remark"     => "海南 保亭"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "琼中",
                            "city_code"  => "A36",
                            "remark"     => "海南 琼中"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "万州",
                            "city_code"  => "1",
                            "remark"     => "重庆 万州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "涪陵",
                            "city_code"  => "2",
                            "remark"     => "重庆 涪陵"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "渝中",
                            "city_code"  => "3",
                            "remark"     => "重庆 渝中"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大渡口",
                            "city_code"  => "4",
                            "remark"     => "重庆 大渡口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "江北",
                            "city_code"  => "5",
                            "remark"     => "重庆 江北"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "沙坪坝",
                            "city_code"  => "6",
                            "remark"     => "重庆 沙坪坝"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "九龙坡",
                            "city_code"  => "7",
                            "remark"     => "重庆 九龙坡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南岸",
                            "city_code"  => "8",
                            "remark"     => "重庆 南岸"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "北碚",
                            "city_code"  => "9",
                            "remark"     => "重庆 北碚"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "两江新区",
                            "city_code"  => "85",
                            "remark"     => "重庆 两江新区"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "万盛",
                            "city_code"  => "10",
                            "remark"     => "重庆 万盛"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "双桥",
                            "city_code"  => "11",
                            "remark"     => "重庆 双桥"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "渝北",
                            "city_code"  => "12",
                            "remark"     => "重庆 渝北"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "巴南",
                            "city_code"  => "13",
                            "remark"     => "重庆 巴南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "长寿",
                            "city_code"  => "21",
                            "remark"     => "重庆 长寿"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "綦江",
                            "city_code"  => "22",
                            "remark"     => "重庆 綦江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "潼南",
                            "city_code"  => "23",
                            "remark"     => "重庆 潼南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "铜梁",
                            "city_code"  => "24",
                            "remark"     => "重庆 铜梁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大足",
                            "city_code"  => "25",
                            "remark"     => "重庆 大足"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "荣昌",
                            "city_code"  => "26",
                            "remark"     => "重庆 荣昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "璧山",
                            "city_code"  => "27",
                            "remark"     => "重庆 璧山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "梁平",
                            "city_code"  => "28",
                            "remark"     => "重庆 梁平"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "城口",
                            "city_code"  => "29",
                            "remark"     => "重庆 城口"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "丰都",
                            "city_code"  => "30",
                            "remark"     => "重庆 丰都"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "垫江",
                            "city_code"  => "31",
                            "remark"     => "重庆 垫江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "武隆",
                            "city_code"  => "32",
                            "remark"     => "重庆 武隆"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "忠县",
                            "city_code"  => "33",
                            "remark"     => "重庆 忠县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "开县",
                            "city_code"  => "34",
                            "remark"     => "重庆 开县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "云阳",
                            "city_code"  => "35",
                            "remark"     => "重庆 云阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "奉节",
                            "city_code"  => "36",
                            "remark"     => "重庆 奉节"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "巫山",
                            "city_code"  => "37",
                            "remark"     => "重庆 巫山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "巫溪",
                            "city_code"  => "38",
                            "remark"     => "重庆 巫溪"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黔江",
                            "city_code"  => "39",
                            "remark"     => "重庆 黔江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "石柱",
                            "city_code"  => "40",
                            "remark"     => "重庆 石柱"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "秀山",
                            "city_code"  => "41",
                            "remark"     => "重庆 秀山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "酉阳",
                            "city_code"  => "42",
                            "remark"     => "重庆 酉阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "彭水",
                            "city_code"  => "43",
                            "remark"     => "重庆 彭水"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "江津",
                            "city_code"  => "81",
                            "remark"     => "重庆 江津"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "合川",
                            "city_code"  => "82",
                            "remark"     => "重庆 合川"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "永川",
                            "city_code"  => "83",
                            "remark"     => "重庆 永川"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南川",
                            "city_code"  => "84",
                            "remark"     => "重庆 南川"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "成都",
                            "city_code"  => "1",
                            "remark"     => "四川 成都"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "自贡",
                            "city_code"  => "3",
                            "remark"     => "四川 自贡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "攀枝花",
                            "city_code"  => "4",
                            "remark"     => "四川 攀枝花"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "泸州",
                            "city_code"  => "5",
                            "remark"     => "四川 泸州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "德阳",
                            "city_code"  => "6",
                            "remark"     => "四川 德阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "绵阳",
                            "city_code"  => "7",
                            "remark"     => "四川 绵阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "广元",
                            "city_code"  => "8",
                            "remark"     => "四川 广元"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "遂宁",
                            "city_code"  => "9",
                            "remark"     => "四川 遂宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "内江",
                            "city_code"  => "10",
                            "remark"     => "四川 内江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "乐山",
                            "city_code"  => "11",
                            "remark"     => "四川 乐山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南充",
                            "city_code"  => "13",
                            "remark"     => "四川 南充"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "眉山",
                            "city_code"  => "14",
                            "remark"     => "四川 眉山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宜宾",
                            "city_code"  => "15",
                            "remark"     => "四川 宜宾"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "广安",
                            "city_code"  => "16",
                            "remark"     => "四川 广安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "达州",
                            "city_code"  => "17",
                            "remark"     => "四川 达州"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "雅安",
                            "city_code"  => "18",
                            "remark"     => "四川 雅安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "巴中",
                            "city_code"  => "19",
                            "remark"     => "四川 巴中"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "资阳",
                            "city_code"  => "20",
                            "remark"     => "四川 资阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阿坝",
                            "city_code"  => "32",
                            "remark"     => "四川 阿坝"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "甘孜",
                            "city_code"  => "33",
                            "remark"     => "四川 甘孜"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "凉山",
                            "city_code"  => "34",
                            "remark"     => "四川 凉山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "贵阳",
                            "city_code"  => "1",
                            "remark"     => "贵州 贵阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "六盘水",
                            "city_code"  => "2",
                            "remark"     => "贵州 六盘水"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "遵义",
                            "city_code"  => "3",
                            "remark"     => "贵州 遵义"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "安顺",
                            "city_code"  => "4",
                            "remark"     => "贵州 安顺"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "铜仁",
                            "city_code"  => "22",
                            "remark"     => "贵州 铜仁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黔西南",
                            "city_code"  => "23",
                            "remark"     => "贵州 黔西南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "毕节",
                            "city_code"  => "24",
                            "remark"     => "贵州 毕节"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黔东南",
                            "city_code"  => "26",
                            "remark"     => "贵州 黔东南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黔南",
                            "city_code"  => "27",
                            "remark"     => "贵州 黔南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "昆明",
                            "city_code"  => "1",
                            "remark"     => "云南 昆明"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "曲靖",
                            "city_code"  => "3",
                            "remark"     => "云南 曲靖"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "玉溪",
                            "city_code"  => "4",
                            "remark"     => "云南 玉溪"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "保山",
                            "city_code"  => "5",
                            "remark"     => "云南 保山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "昭通",
                            "city_code"  => "6",
                            "remark"     => "云南 昭通"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "丽江",
                            "city_code"  => "7",
                            "remark"     => "云南 丽江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "普洱",
                            "city_code"  => "8",
                            "remark"     => "云南 普洱"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "临沧",
                            "city_code"  => "9",
                            "remark"     => "云南 临沧"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "楚雄",
                            "city_code"  => "23",
                            "remark"     => "云南 楚雄"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "红河",
                            "city_code"  => "25",
                            "remark"     => "云南 红河"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "文山",
                            "city_code"  => "26",
                            "remark"     => "云南 文山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "西双版纳",
                            "city_code"  => "28",
                            "remark"     => "云南 西双版纳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "大理",
                            "city_code"  => "29",
                            "remark"     => "云南 大理"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "德宏",
                            "city_code"  => "31",
                            "remark"     => "云南 德宏"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "怒江",
                            "city_code"  => "33",
                            "remark"     => "云南 怒江"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "迪庆",
                            "city_code"  => "34",
                            "remark"     => "云南 迪庆"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "拉萨",
                            "city_code"  => "1",
                            "remark"     => "西藏 拉萨"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "昌都",
                            "city_code"  => "21",
                            "remark"     => "西藏 昌都"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "山南",
                            "city_code"  => "22",
                            "remark"     => "西藏 山南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "日喀则",
                            "city_code"  => "23",
                            "remark"     => "西藏 日喀则"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "那曲",
                            "city_code"  => "24",
                            "remark"     => "西藏 那曲"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阿里",
                            "city_code"  => "25",
                            "remark"     => "西藏 阿里"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "林芝",
                            "city_code"  => "26",
                            "remark"     => "西藏 林芝"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "西安",
                            "city_code"  => "1",
                            "remark"     => "陕西 西安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "铜川",
                            "city_code"  => "2",
                            "remark"     => "陕西 铜川"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宝鸡",
                            "city_code"  => "3",
                            "remark"     => "陕西 宝鸡"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "咸阳",
                            "city_code"  => "4",
                            "remark"     => "陕西 咸阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "渭南",
                            "city_code"  => "5",
                            "remark"     => "陕西 渭南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "延安",
                            "city_code"  => "6",
                            "remark"     => "陕西 延安"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "汉中",
                            "city_code"  => "7",
                            "remark"     => "陕西 汉中"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "榆林",
                            "city_code"  => "8",
                            "remark"     => "陕西 榆林"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "安康",
                            "city_code"  => "9",
                            "remark"     => "陕西 安康"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "商洛",
                            "city_code"  => "10",
                            "remark"     => "陕西 商洛"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "兰州市",
                            "city_code"  => "1",
                            "remark"     => "甘肃 兰州市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "嘉峪关",
                            "city_code"  => "2",
                            "remark"     => "甘肃 嘉峪关"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "金昌",
                            "city_code"  => "3",
                            "remark"     => "甘肃 金昌"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "白银",
                            "city_code"  => "4",
                            "remark"     => "甘肃 白银"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "天水",
                            "city_code"  => "5",
                            "remark"     => "甘肃 天水"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "武威",
                            "city_code"  => "6",
                            "remark"     => "甘肃 武威"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "张掖",
                            "city_code"  => "7",
                            "remark"     => "甘肃 张掖"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "平凉",
                            "city_code"  => "8",
                            "remark"     => "甘肃 平凉"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "酒泉",
                            "city_code"  => "9",
                            "remark"     => "甘肃 酒泉"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "庆阳",
                            "city_code"  => "10",
                            "remark"     => "甘肃 庆阳"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "定西",
                            "city_code"  => "11",
                            "remark"     => "甘肃 定西"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "陇南",
                            "city_code"  => "12",
                            "remark"     => "甘肃 陇南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "临夏",
                            "city_code"  => "29",
                            "remark"     => "甘肃 临夏"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "甘南",
                            "city_code"  => "30",
                            "remark"     => "甘肃 甘南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "西宁",
                            "city_code"  => "1",
                            "remark"     => "青海 西宁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "海东",
                            "city_code"  => "21",
                            "remark"     => "青海 海东"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "海北",
                            "city_code"  => "22",
                            "remark"     => "青海 海北"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "黄南",
                            "city_code"  => "23",
                            "remark"     => "青海 黄南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "海南",
                            "city_code"  => "25",
                            "remark"     => "青海 海南"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "果洛",
                            "city_code"  => "26",
                            "remark"     => "青海 果洛"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "玉树",
                            "city_code"  => "27",
                            "remark"     => "青海 玉树"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "海西",
                            "city_code"  => "28",
                            "remark"     => "青海 海西"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "银川",
                            "city_code"  => "1",
                            "remark"     => "宁夏 银川"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "石嘴山",
                            "city_code"  => "2",
                            "remark"     => "宁夏 石嘴山"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "吴忠",
                            "city_code"  => "3",
                            "remark"     => "宁夏 吴忠"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "固原",
                            "city_code"  => "4",
                            "remark"     => "宁夏 固原"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "中卫",
                            "city_code"  => "5",
                            "remark"     => "宁夏 中卫"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "乌鲁木齐",
                            "city_code"  => "1",
                            "remark"     => "新疆 乌鲁木齐"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "克拉玛依",
                            "city_code"  => "2",
                            "remark"     => "新疆 克拉玛依"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "吐鲁番",
                            "city_code"  => "21",
                            "remark"     => "新疆 吐鲁番"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "哈密",
                            "city_code"  => "22",
                            "remark"     => "新疆 哈密"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "昌吉",
                            "city_code"  => "23",
                            "remark"     => "新疆 昌吉"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "博尔塔拉",
                            "city_code"  => "27",
                            "remark"     => "新疆 博尔塔拉"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "巴音郭楞",
                            "city_code"  => "28",
                            "remark"     => "新疆 巴音郭楞"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阿克苏",
                            "city_code"  => "29",
                            "remark"     => "新疆 阿克苏"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "克孜勒苏",
                            "city_code"  => "30",
                            "remark"     => "新疆 克孜勒苏"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "喀什",
                            "city_code"  => "31",
                            "remark"     => "新疆 喀什"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "和田",
                            "city_code"  => "32",
                            "remark"     => "新疆 和田"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "伊犁",
                            "city_code"  => "40",
                            "remark"     => "新疆 伊犁"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "塔城",
                            "city_code"  => "42",
                            "remark"     => "新疆 塔城"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阿勒泰",
                            "city_code"  => "43",
                            "remark"     => "新疆 阿勒泰"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "石河子",
                            "city_code"  => "91",
                            "remark"     => "新疆 石河子"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "阿拉尔",
                            "city_code"  => "92",
                            "remark"     => "新疆 阿拉尔"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "图木舒克",
                            "city_code"  => "93",
                            "remark"     => "新疆 图木舒克"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "五家渠",
                            "city_code"  => "94",
                            "remark"     => "新疆 五家渠"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "北屯",
                            "city_code"  => "95",
                            "remark"     => "新疆 北屯"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台北市",
                            "city_code"  => "1",
                            "remark"     => "台湾 台北市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "高雄市",
                            "city_code"  => "2",
                            "remark"     => "台湾 高雄市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "基隆市",
                            "city_code"  => "3",
                            "remark"     => "台湾 基隆市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台中市",
                            "city_code"  => "4",
                            "remark"     => "台湾 台中市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台南市",
                            "city_code"  => "5",
                            "remark"     => "台湾 台南市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "新竹市",
                            "city_code"  => "6",
                            "remark"     => "台湾 新竹市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "嘉义市",
                            "city_code"  => "7",
                            "remark"     => "台湾 嘉义市"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台北县",
                            "city_code"  => "8",
                            "remark"     => "台湾 台北县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "宜兰县",
                            "city_code"  => "9",
                            "remark"     => "台湾 宜兰县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "桃园县",
                            "city_code"  => "10",
                            "remark"     => "台湾 桃园县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "新竹县",
                            "city_code"  => "11",
                            "remark"     => "台湾 新竹县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "苗栗县",
                            "city_code"  => "12",
                            "remark"     => "台湾 苗栗县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台中县",
                            "city_code"  => "13",
                            "remark"     => "台湾 台中县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "彰化县",
                            "city_code"  => "14",
                            "remark"     => "台湾 彰化县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "南投县",
                            "city_code"  => "15",
                            "remark"     => "台湾 南投县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "云林县",
                            "city_code"  => "16",
                            "remark"     => "台湾 云林县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "嘉义县",
                            "city_code"  => "17",
                            "remark"     => "台湾 嘉义县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台南县",
                            "city_code"  => "18",
                            "remark"     => "台湾 台南县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "高雄县",
                            "city_code"  => "19",
                            "remark"     => "台湾 高雄县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "屏东县",
                            "city_code"  => "20",
                            "remark"     => "台湾 屏东县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "台东县",
                            "city_code"  => "22",
                            "remark"     => "台湾 台东县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "花莲县",
                            "city_code"  => "23",
                            "remark"     => "台湾 花莲县"
                        ],
                        [
                            "country_id" => 1,
                            "currency"   => "CNY",
                            "city"       => "澎湖县",
                            "city_code"  => "21",
                            "remark"     => "台湾 澎湖县"
                        ]
                    ];
                    \App\Models\City::query()->insert($cities);
                    break;
                case 2:
                    $cities = [
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Đắk Nông",
                            "city_code"  => "Đắk Nông",
                            "remark"     => "Đắk Nông"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bình Phước",
                            "city_code"  => "Bình Phước",
                            "remark"     => "Bình Phước"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Đà Nẵng",
                            "city_code"  => "Đà Nẵng",
                            "remark"     => "Đà Nẵng"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hồ Chí Minh City",
                            "city_code"  => "Hồ Chí Minh City",
                            "remark"     => "Hồ Chí Minh City"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hà Nội",
                            "city_code"  => "Hà Nội",
                            "remark"     => "Hà Nội"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hải Phòng",
                            "city_code"  => "Hải Phòng",
                            "remark"     => "Hải Phòng"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Quảng Nam",
                            "city_code"  => "Quảng Nam",
                            "remark"     => "Quảng Nam"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Cần Thơ",
                            "city_code"  => "Cần Thơ",
                            "remark"     => "Cần Thơ"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Yên Bái",
                            "city_code"  => "Yên Bái",
                            "remark"     => "Yên Bái"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bà Rịa–Vũng Tàu",
                            "city_code"  => "Bà Rịa–Vũng Tàu",
                            "remark"     => "Bà Rịa–Vũng Tàu"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Vĩnh Phúc",
                            "city_code"  => "Vĩnh Phúc",
                            "remark"     => "Vĩnh Phúc"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Vĩnh Long",
                            "city_code"  => "Vĩnh Long",
                            "remark"     => "Vĩnh Long"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Nghệ An",
                            "city_code"  => "Nghệ An",
                            "remark"     => "Nghệ An"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Phú Thọ",
                            "city_code"  => "Phú Thọ",
                            "remark"     => "Phú Thọ"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hậu Giang",
                            "city_code"  => "Hậu Giang",
                            "remark"     => "Hậu Giang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Quảng Ninh",
                            "city_code"  => "Quảng Ninh",
                            "remark"     => "Quảng Ninh"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Tuyên Quang",
                            "city_code"  => "Tuyên Quang",
                            "remark"     => "Tuyên Quang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Phú Yên",
                            "city_code"  => "Phú Yên",
                            "remark"     => "Phú Yên"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Trà Vinh",
                            "city_code"  => "Trà Vinh",
                            "remark"     => "Trà Vinh"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bình Dương",
                            "city_code"  => "Bình Dương",
                            "remark"     => "Bình Dương"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Thanh Hóa",
                            "city_code"  => "Thanh Hóa",
                            "remark"     => "Thanh Hóa"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Thái Nguyên",
                            "city_code"  => "Thái Nguyên",
                            "remark"     => "Thái Nguyên"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Thái Bình",
                            "city_code"  => "Thái Bình",
                            "remark"     => "Thái Bình"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Tây Ninh",
                            "city_code"  => "Tây Ninh",
                            "remark"     => "Tây Ninh"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Long An",
                            "city_code"  => "Long An",
                            "remark"     => "Long An"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Ninh Bình",
                            "city_code"  => "Ninh Bình",
                            "remark"     => "Ninh Bình"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Sơn La",
                            "city_code"  => "Sơn La",
                            "remark"     => "Sơn La"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Sóc Trăng",
                            "city_code"  => "Sóc Trăng",
                            "remark"     => "Sóc Trăng"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Đồng Tháp",
                            "city_code"  => "Đồng Tháp",
                            "remark"     => "Đồng Tháp"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Kiên Giang",
                            "city_code"  => "Kiên Giang",
                            "remark"     => "Kiên Giang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bình Định",
                            "city_code"  => "Bình Định",
                            "remark"     => "Bình Định"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Quảng Ngãi",
                            "city_code"  => "Quảng Ngãi",
                            "remark"     => "Quảng Ngãi"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Gia Lai",
                            "city_code"  => "Gia Lai",
                            "remark"     => "Gia Lai"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hà Nam",
                            "city_code"  => "Hà Nam",
                            "remark"     => "Hà Nam"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bình Thuận",
                            "city_code"  => "Bình Thuận",
                            "remark"     => "Bình Thuận"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Ninh Thuận",
                            "city_code"  => "Ninh Thuận",
                            "remark"     => "Ninh Thuận"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Khánh Hòa",
                            "city_code"  => "Khánh Hòa",
                            "remark"     => "Khánh Hòa"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Nam Định",
                            "city_code"  => "Nam Định",
                            "remark"     => "Nam Định"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Tiền Giang",
                            "city_code"  => "Tiền Giang",
                            "remark"     => "Tiền Giang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "An Giang",
                            "city_code"  => "An Giang",
                            "remark"     => "An Giang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Lào Cai",
                            "city_code"  => "Lào Cai",
                            "remark"     => "Lào Cai"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Lạng Sơn",
                            "city_code"  => "Lạng Sơn",
                            "remark"     => "Lạng Sơn"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Lai Châu",
                            "city_code"  => "Lai Châu",
                            "remark"     => "Lai Châu"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Kon Tum",
                            "city_code"  => "Kon Tum",
                            "remark"     => "Kon Tum"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hưng Yên",
                            "city_code"  => "Hưng Yên",
                            "remark"     => "Hưng Yên"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Thừa Thiên–Huế",
                            "city_code"  => "Thừa Thiên–Huế",
                            "remark"     => "Thừa Thiên–Huế"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hòa Bình",
                            "city_code"  => "Hòa Bình",
                            "remark"     => "Hòa Bình"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hải Dương",
                            "city_code"  => "Hải Dương",
                            "remark"     => "Hải Dương"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hà Tĩnh",
                            "city_code"  => "Hà Tĩnh",
                            "remark"     => "Hà Tĩnh"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Hà Giang",
                            "city_code"  => "Hà Giang",
                            "remark"     => "Hà Giang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Quảng Bình",
                            "city_code"  => "Quảng Bình",
                            "remark"     => "Quảng Bình"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Quảng Trị",
                            "city_code"  => "Quảng Trị",
                            "remark"     => "Quảng Trị"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Điện Biên",
                            "city_code"  => "Điện Biên",
                            "remark"     => "Điện Biên"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Lâm Đồng",
                            "city_code"  => "Lâm Đồng",
                            "remark"     => "Lâm Đồng"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Cao Bằng",
                            "city_code"  => "Cao Bằng",
                            "remark"     => "Cao Bằng"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Cà Mau",
                            "city_code"  => "Cà Mau",
                            "remark"     => "Cà Mau"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Đắk Lắk",
                            "city_code"  => "Đắk Lắk",
                            "remark"     => "Đắk Lắk"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Đồng Nai",
                            "city_code"  => "Đồng Nai",
                            "remark"     => "Đồng Nai"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bến Tre",
                            "city_code"  => "Bến Tre",
                            "remark"     => "Bến Tre"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bắc Ninh",
                            "city_code"  => "Bắc Ninh",
                            "remark"     => "Bắc Ninh"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bắc Kạn",
                            "city_code"  => "Bắc Kạn",
                            "remark"     => "Bắc Kạn"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bắc Giang",
                            "city_code"  => "Bắc Giang",
                            "remark"     => "Bắc Giang"
                        ],
                        [
                            "country_id" => 2,
                            "currency"   => "VND",
                            "city"       => "Bạc Liêu",
                            "city_code"  => "Bạc Liêu",
                            "remark"     => "Bạc Liêu"
                        ]
                    ];
                    \App\Models\City::query()->insert($cities);
                    break;
                case 3:
                    $cities = [
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fayetteville",
                            "city_code"  => "FYV",
                            "remark"     => "Fayetteville Arkansas,费耶特维尔 阿肯色"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fort Smith",
                            "city_code"  => "FSM",
                            "remark"     => "Fort Smith Arkansas,史密斯堡 阿肯色"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Little Rock",
                            "city_code"  => "LIT",
                            "remark"     => "Little Rock Arkansas,小石城 阿肯色"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Birmingham",
                            "city_code"  => "BHM",
                            "remark"     => "Birmingham Alabama,伯明罕 阿拉巴马"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Montgomery",
                            "city_code"  => "MGM",
                            "remark"     => "Montgomery Alabama,蒙哥马利 阿拉巴马"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Mobile",
                            "city_code"  => "MOB",
                            "remark"     => "Mobile Alabama,莫比尔 阿拉巴马"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Anchorage",
                            "city_code"  => "ANC",
                            "remark"     => "Anchorage Alaska,安克雷奇 阿拉斯加"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fairbanks",
                            "city_code"  => "FAI",
                            "remark"     => "Fairbanks Alaska,费尔班克斯 阿拉斯加"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Juneau",
                            "city_code"  => "JNU",
                            "remark"     => "Juneau Alaska,朱诺 阿拉斯加"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Idaho Falls",
                            "city_code"  => "IDA",
                            "remark"     => "Idaho Falls Idaho,爱达荷福尔斯 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Pocatello",
                            "city_code"  => "PIH",
                            "remark"     => "Pocatello Idaho,波卡特洛 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Boise",
                            "city_code"  => "BOI",
                            "remark"     => "Boise Idaho,博伊西 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Blackfoot",
                            "city_code"  => "BLK",
                            "remark"     => "Blackfoot Idaho,布莱克富特 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Coeur d'Alene",
                            "city_code"  => "COE",
                            "remark"     => "Coeur d'Alene Idaho,科达伦 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Lewiston",
                            "city_code"  => "LWS",
                            "remark"     => "Lewiston Idaho,刘易斯顿 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Moscow",
                            "city_code"  => "MJL",
                            "remark"     => "Moscow Idaho,莫斯科 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Murphy",
                            "city_code"  => "ZMU",
                            "remark"     => "Murphy Idaho,墨菲 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Nampa",
                            "city_code"  => "NPA",
                            "remark"     => "Nampa Idaho,楠帕 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Ketchum",
                            "city_code"  => "QKM",
                            "remark"     => "Ketchum Idaho,岂彻姆 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sun Valley",
                            "city_code"  => "SVY",
                            "remark"     => "Sun Valley Idaho,森瓦利 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "American Falls",
                            "city_code"  => "YAF",
                            "remark"     => "American Falls Idaho,亚美利加瀑布城 爱达荷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Davenport",
                            "city_code"  => "DVN",
                            "remark"     => "Davenport Iowa,达文波特 爱荷华"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Des Moines",
                            "city_code"  => "DSM",
                            "remark"     => "Des Moines Iowa,得梅因 爱荷华"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Cedar Rapids",
                            "city_code"  => "CID",
                            "remark"     => "Cedar Rapids Iowa,锡达拉皮兹 爱荷华"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bismarck",
                            "city_code"  => "BIS",
                            "remark"     => "Bismarck North Dakota,俾斯麦 北达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Grand Forks",
                            "city_code"  => "GFK",
                            "remark"     => "Grand Forks North Dakota,大福克斯 北达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fargo",
                            "city_code"  => "FAR",
                            "remark"     => "Fargo North Dakota,法戈 北达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Minot",
                            "city_code"  => "MOT",
                            "remark"     => "Minot North Dakota,迈诺特 北达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Asheville",
                            "city_code"  => "AEV",
                            "remark"     => "Asheville North Carolina,艾许维尔 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Durham",
                            "city_code"  => "DHH",
                            "remark"     => "Durham North Carolina,杜罕 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Greensboro",
                            "city_code"  => "GBO",
                            "remark"     => "Greensboro North Carolina,格林斯伯勒 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Chapel Hill",
                            "city_code"  => "CHE",
                            "remark"     => "Chapel Hill North Carolina,教堂山 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Raleigh",
                            "city_code"  => "RAG",
                            "remark"     => "Raleigh North Carolina,罗利 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Raleigh-Durham",
                            "city_code"  => "RDU",
                            "remark"     => "Raleigh-Durham North Carolina,洛利杜罕都会区 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Charlotte",
                            "city_code"  => "CRQ",
                            "remark"     => "Charlotte North Carolina,夏洛特 北卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Allentown",
                            "city_code"  => "AEW",
                            "remark"     => "Allentown Pennsylvania,阿伦敦 宾夕法尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Philadephia",
                            "city_code"  => "PHL",
                            "remark"     => "Philadephia Pennsylvania,费城 宾夕法尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Pittsburgh",
                            "city_code"  => "PIT",
                            "remark"     => "Pittsburgh Pennsylvania,匹兹堡 宾夕法尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "El Paso",
                            "city_code"  => "ELP",
                            "remark"     => "El Paso Texas,埃尔帕索 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Austin",
                            "city_code"  => "AUS",
                            "remark"     => "Austin Texas,奥斯汀 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Dallas",
                            "city_code"  => "DAL",
                            "remark"     => "Dallas Texas,达拉斯 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Corpus Christi",
                            "city_code"  => "CRP",
                            "remark"     => "Corpus Christi Texas,哥帕斯基斯蒂 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Galveston",
                            "city_code"  => "GLS",
                            "remark"     => "Galveston Texas,交维斯顿 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Laredo",
                            "city_code"  => "LRD",
                            "remark"     => "Laredo Texas,拉雷多 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "McAllen",
                            "city_code"  => "TXC",
                            "remark"     => "McAllen Texas,麦亚伦 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "San Antonio",
                            "city_code"  => "SAT",
                            "remark"     => "San Antonio Texas,圣安东尼奥 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Houston",
                            "city_code"  => "HOU",
                            "remark"     => "Houston Texas,休斯敦 德克萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Dayton",
                            "city_code"  => "DYT",
                            "remark"     => "Dayton Ohio,代顿 俄亥俄"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Columbus",
                            "city_code"  => "CZX",
                            "remark"     => "Columbus Ohio,哥伦布 俄亥俄"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Cleveland",
                            "city_code"  => "CLE",
                            "remark"     => "Cleveland Ohio,克利夫兰 俄亥俄"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Toledo",
                            "city_code"  => "TOL",
                            "remark"     => "Toledo Ohio,托莱多 俄亥俄"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Cincinnati",
                            "city_code"  => "CVG",
                            "remark"     => "Cincinnati Ohio,辛辛那提 俄亥俄"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Oklahoma City",
                            "city_code"  => "OKC",
                            "remark"     => "Oklahoma City Oklahoma,俄克拉荷马城 俄克拉荷马"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Norman",
                            "city_code"  => "OUN",
                            "remark"     => "Norman Oklahoma,诺曼 俄克拉荷马"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tulsa",
                            "city_code"  => "TUL",
                            "remark"     => "Tulsa Oklahoma,塔尔萨 俄克拉荷马"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bend",
                            "city_code"  => "BZO",
                            "remark"     => "Bend Oregon,本德 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Portland",
                            "city_code"  => "PDX",
                            "remark"     => "Portland Oregon,波特兰 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "The Dalles",
                            "city_code"  => "DLS",
                            "remark"     => "The Dalles Oregon,达尔斯 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Dallas",
                            "city_code"  => "DAC",
                            "remark"     => "Dallas Oregon,达拉斯 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tillamook",
                            "city_code"  => "TLM",
                            "remark"     => "Tillamook Oregon,蒂拉穆克 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Grant's Pass",
                            "city_code"  => "XFX",
                            "remark"     => "Grant's Pass Oregon,格兰茨帕斯 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Hood River",
                            "city_code"  => "HDX",
                            "remark"     => "Hood River Oregon,胡德里弗 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Crater Lake",
                            "city_code"  => "CTR",
                            "remark"     => "Crater Lake Oregon,火山口湖 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Corvallis",
                            "city_code"  => "YCV",
                            "remark"     => "Corvallis Oregon,科瓦利斯 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Coos Bay",
                            "city_code"  => "COB",
                            "remark"     => "Coos Bay Oregon,库斯贝 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Medford",
                            "city_code"  => "MFR",
                            "remark"     => "Medford Oregon,梅德福 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Salem",
                            "city_code"  => "SLE",
                            "remark"     => "Salem Oregon,塞勒姆 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "St Helens",
                            "city_code"  => "STH",
                            "remark"     => "St Helens Oregon,圣海伦斯 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Springfield",
                            "city_code"  => "SPY",
                            "remark"     => "Springfield Oregon,斯普林菲尔德 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Eugene",
                            "city_code"  => "EUG",
                            "remark"     => "Eugene Oregon,尤金 俄勒冈"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Orlando",
                            "city_code"  => "ORL",
                            "remark"     => "Orlando Florida,奥兰多 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Key West",
                            "city_code"  => "EYW",
                            "remark"     => "Key West Florida,基韦斯特 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Jacksonville",
                            "city_code"  => "JAX",
                            "remark"     => "Jacksonville Florida,杰克逊维尔 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Cape Canaveral",
                            "city_code"  => "CPV",
                            "remark"     => "Cape Canaveral Florida,卡纳维尔角 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fort Lauderdale",
                            "city_code"  => "FLL",
                            "remark"     => "Fort Lauderdale Florida,罗德岱堡 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Miami",
                            "city_code"  => "MIA",
                            "remark"     => "Miami Florida,迈阿密 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "St. Petersburg",
                            "city_code"  => "PIE",
                            "remark"     => "St. Petersburg Florida,圣彼德斯堡市 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tallahassee",
                            "city_code"  => "TLH",
                            "remark"     => "Tallahassee Florida,塔拉哈西 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tampa",
                            "city_code"  => "TPA",
                            "remark"     => "Tampa Florida,坦帕 佛罗里达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Burlington",
                            "city_code"  => "BTV",
                            "remark"     => "Burlington Vermont,伯灵顿 佛蒙特"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rutland",
                            "city_code"  => "RUT",
                            "remark"     => "Rutland Vermont,拉特兰 佛蒙特"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "South Burlington",
                            "city_code"  => "ZBR",
                            "remark"     => "South Burlington Vermont,南伯灵顿 佛蒙特"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Washington D.C.",
                            "city_code"  => "WAS",
                            "remark"     => "Washington D.C. District of Columbia,华盛顿哥伦比亚特区 哥伦比亚特区"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Spokane",
                            "city_code"  => "GEG",
                            "remark"     => "Spokane Washington,斯波坎 华盛顿"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tacoma",
                            "city_code"  => "TTW",
                            "remark"     => "Tacoma Washington,塔科马 华盛顿"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Seattle",
                            "city_code"  => "SEA",
                            "remark"     => "Seattle Washington,西雅图 华盛顿"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Evanston",
                            "city_code"  => "EVD",
                            "remark"     => "Evanston Wyoming,埃文斯顿 怀俄明"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Casper",
                            "city_code"  => "CPR",
                            "remark"     => "Casper Wyoming,卡斯珀 怀俄明"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Laramie",
                            "city_code"  => "LAR",
                            "remark"     => "Laramie Wyoming,拉勒米 怀俄明"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rock Springs",
                            "city_code"  => "RKS",
                            "remark"     => "Rock Springs Wyoming,罗克斯普林斯 怀俄明"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Cheyenne",
                            "city_code"  => "CYS",
                            "remark"     => "Cheyenne Wyoming,夏延 怀俄明"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sheridan",
                            "city_code"  => "SHR",
                            "remark"     => "Sheridan Wyoming,谢里登 怀俄明"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "San Francisco",
                            "city_code"  => "SFO",
                            "remark"     => "San Francisco California,旧金山 加利福尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Los Angeles",
                            "city_code"  => "LAX",
                            "remark"     => "Los Angeles California,洛杉矶 加利福尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "San Diego",
                            "city_code"  => "SAN",
                            "remark"     => "San Diego California,圣迭戈 加利福尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "San Jose",
                            "city_code"  => "SJC",
                            "remark"     => "San Jose California,圣何塞 加利福尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Abilene",
                            "city_code"  => "ABZ",
                            "remark"     => "Abilene Kansas,阿比林 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Overland Park",
                            "city_code"  => "OVL",
                            "remark"     => "Overland Park Kansas,奥弗兰公园 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Hutchinson",
                            "city_code"  => "HCH",
                            "remark"     => "Hutchinson Kansas,哈钦森 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Kansas City",
                            "city_code"  => "KCK",
                            "remark"     => "Kansas City Kansas,堪萨斯城 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Leavenworth",
                            "city_code"  => "XIA",
                            "remark"     => "Leavenworth Kansas,莱文沃思 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Lawrence",
                            "city_code"  => "LWC",
                            "remark"     => "Lawrence Kansas,劳伦斯 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Manhattan",
                            "city_code"  => "MHK",
                            "remark"     => "Manhattan Kansas,曼哈顿 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Topeka",
                            "city_code"  => "TOP",
                            "remark"     => "Topeka Kansas,托皮卡 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Wichita",
                            "city_code"  => "ICT",
                            "remark"     => "Wichita Kansas,威奇托 堪萨斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bridgeport",
                            "city_code"  => "BDR",
                            "remark"     => "Bridgeport Connecticut,布里奇波特 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Darien",
                            "city_code"  => "DAQ",
                            "remark"     => "Darien Connecticut,达里恩 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Greenwich",
                            "city_code"  => "GRH",
                            "remark"     => "Greenwich Connecticut,格林尼治 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Hartford",
                            "city_code"  => "HFD",
                            "remark"     => "Hartford Connecticut,哈特福德 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Middletown",
                            "city_code"  => "XIN",
                            "remark"     => "Middletown Connecticut,米德尔顿 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "New Haven",
                            "city_code"  => "HVN",
                            "remark"     => "New Haven Connecticut,纽黑文 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Westport",
                            "city_code"  => "WPT",
                            "remark"     => "Westport Connecticut,韦斯特波特 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Waterbury",
                            "city_code"  => "WAT",
                            "remark"     => "Waterbury Connecticut,沃特伯里 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "New Britain",
                            "city_code"  => "NWT",
                            "remark"     => "New Britain Connecticut,新不列颠 康涅狄格"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Aspen",
                            "city_code"  => "ASE",
                            "remark"     => "Aspen Colorado,阿斯彭 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Aurora",
                            "city_code"  => "AUX",
                            "remark"     => "Aurora Colorado,奥罗拉 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Boulder",
                            "city_code"  => "WBU",
                            "remark"     => "Boulder Colorado,博尔德 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Grand Junction",
                            "city_code"  => "GJT",
                            "remark"     => "Grand Junction Colorado,大章克申 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Denver",
                            "city_code"  => "DEN",
                            "remark"     => "Denver Colorado,丹佛 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fort Collins",
                            "city_code"  => "FNL",
                            "remark"     => "Fort Collins Colorado,柯林斯堡 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Colorado Springs",
                            "city_code"  => "COS",
                            "remark"     => "Colorado Springs Colorado,科罗拉多斯普林斯 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Vail",
                            "city_code"  => "VAC",
                            "remark"     => "Vail Colorado,韦尔 科罗拉多"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Lexington",
                            "city_code"  => "LEX",
                            "remark"     => "Lexington Kentucky,列克星敦 肯塔基"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Louisville",
                            "city_code"  => "LUI",
                            "remark"     => "Louisville Kentucky,路易斯维尔 肯塔基"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Owensboro",
                            "city_code"  => "OWB",
                            "remark"     => "Owensboro Kentucky,欧文斯伯勒 肯塔基"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Baton Rouge",
                            "city_code"  => "BTR",
                            "remark"     => "Baton Rouge Louisiana,巴吞鲁日 路易斯安那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Shreveport",
                            "city_code"  => "SHV",
                            "remark"     => "Shreveport Louisiana,什里夫波特 路易斯安那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "New Orleans",
                            "city_code"  => "MSY",
                            "remark"     => "New Orleans Louisiana,新奥尔良 路易斯安那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Pawtucket",
                            "city_code"  => "PAW",
                            "remark"     => "Pawtucket Rhode Island,波塔基特 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Cranston",
                            "city_code"  => "CQH",
                            "remark"     => "Cranston Rhode Island,克兰斯顿 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Newport",
                            "city_code"  => "NPO",
                            "remark"     => "Newport Rhode Island,纽波特 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Providence",
                            "city_code"  => "PVD",
                            "remark"     => "Providence Rhode Island,普罗维登斯 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Westerly",
                            "city_code"  => "WST",
                            "remark"     => "Westerly Rhode Island,韦斯特利 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Woonsocket",
                            "city_code"  => "SFN",
                            "remark"     => "Woonsocket Rhode Island,文索基特 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Warwick",
                            "city_code"  => "UZO",
                            "remark"     => "Warwick Rhode Island,沃威克 罗德岛"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Balitmore",
                            "city_code"  => "BAL",
                            "remark"     => "Balitmore Maryland,巴尔的摩 马里兰"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Gaithersburg",
                            "city_code"  => "GAI",
                            "remark"     => "Gaithersburg Maryland,盖瑟斯堡 马里兰"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rockville",
                            "city_code"  => "RKV",
                            "remark"     => "Rockville Maryland,罗克维尔 马里兰"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Boston",
                            "city_code"  => "BZD",
                            "remark"     => "Boston Massachusetts,波士顿 马萨诸塞"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Springfield",
                            "city_code"  => "SFY",
                            "remark"     => "Springfield Massachusetts,斯普林菲尔德 马萨诸塞"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Worcester",
                            "city_code"  => "ORH",
                            "remark"     => "Worcester Massachusetts,伍斯特 马萨诸塞"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Billings",
                            "city_code"  => "BGS",
                            "remark"     => "Billings Montana,比灵斯 蒙大拿"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Great Falls",
                            "city_code"  => "GTF",
                            "remark"     => "Great Falls Montana,大瀑布村 蒙大拿"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Missoula",
                            "city_code"  => "MSO",
                            "remark"     => "Missoula Montana,米苏拉 蒙大拿"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Columbia",
                            "city_code"  => "COV",
                            "remark"     => "Columbia Missouri,哥伦比亚 密苏里"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Jefferson City",
                            "city_code"  => "JEF",
                            "remark"     => "Jefferson City Missouri,杰佛逊市 密苏里"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Kansas City",
                            "city_code"  => "MKC",
                            "remark"     => "Kansas City Missouri,堪萨斯城 密苏里"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sanit Louis",
                            "city_code"  => "STL",
                            "remark"     => "Sanit Louis Missouri,圣路易斯 密苏里"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Springfield",
                            "city_code"  => "SGF",
                            "remark"     => "Springfield Missouri,斯普林菲尔德 密苏里"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Biloxi",
                            "city_code"  => "BIX",
                            "remark"     => "Biloxi Mississippi,比洛克西 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Gulfport",
                            "city_code"  => "GPT",
                            "remark"     => "Gulfport Mississippi,格尔夫波特 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Greenville",
                            "city_code"  => "GLH",
                            "remark"     => "Greenville Mississippi,格林维尔 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Hattiesburg",
                            "city_code"  => "HBG",
                            "remark"     => "Hattiesburg Mississippi,哈蒂斯堡 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Jackson",
                            "city_code"  => "JAN",
                            "remark"     => "Jackson Mississippi,杰克逊 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Meridian",
                            "city_code"  => "MEI",
                            "remark"     => "Meridian Mississippi,默里迪恩 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Vicksburg",
                            "city_code"  => "VKS",
                            "remark"     => "Vicksburg Mississippi,维克斯堡 密西西比"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Ann Arbor",
                            "city_code"  => "ARB",
                            "remark"     => "Ann Arbor Michigan,安娜堡 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Battle Creek",
                            "city_code"  => "BTL",
                            "remark"     => "Battle Creek Michigan,巴特尔克里克 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bay City",
                            "city_code"  => "BCY",
                            "remark"     => "Bay City Michigan,贝城 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Grand Rapids",
                            "city_code"  => "GRR",
                            "remark"     => "Grand Rapids Michigan,大急流城 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Dearborn",
                            "city_code"  => "DEO",
                            "remark"     => "Dearborn Michigan,迪尔伯恩 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Detroit",
                            "city_code"  => "DET",
                            "remark"     => "Detroit Michigan,底特律 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Flint",
                            "city_code"  => "FNT",
                            "remark"     => "Flint Michigan,弗林特 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Wyandotte",
                            "city_code"  => "WYD",
                            "remark"     => "Wyandotte Michigan,怀恩多特 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Kalamazoo",
                            "city_code"  => "AZO",
                            "remark"     => "Kalamazoo Michigan,卡拉马袓 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Lansing",
                            "city_code"  => "LAN",
                            "remark"     => "Lansing Michigan,兰辛 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Muskegon",
                            "city_code"  => "MKG",
                            "remark"     => "Muskegon Michigan,马斯基根 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Pontiac",
                            "city_code"  => "PTK",
                            "remark"     => "Pontiac Michigan,庞菷亚克 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Saginaw",
                            "city_code"  => "SGM",
                            "remark"     => "Saginaw Michigan,萨吉诺 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sault Ste Marie",
                            "city_code"  => "SSM",
                            "remark"     => "Sault Ste Marie Michigan,苏圣玛丽 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Warren",
                            "city_code"  => "WAM",
                            "remark"     => "Warren Michigan,沃伦 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Port Huron",
                            "city_code"  => "PHN",
                            "remark"     => "Port Huron Michigan,休伦港 密歇根"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bangor",
                            "city_code"  => "BNQ",
                            "remark"     => "Bangor Maine,班戈 缅因"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Portland",
                            "city_code"  => "POL",
                            "remark"     => "Portland Maine,波特兰 缅因"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Lewiston",
                            "city_code"  => "QLW",
                            "remark"     => "Lewiston Maine,刘易斯顿 缅因"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rochester",
                            "city_code"  => "RST",
                            "remark"     => "Rochester Minnesota,罗切斯特 明尼苏达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Minneapolis",
                            "city_code"  => "MES",
                            "remark"     => "Minneapolis Minnesota,明尼阿波利斯 明尼苏达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Saint Paul",
                            "city_code"  => "STP",
                            "remark"     => "Saint Paul Minnesota,圣保罗 明尼苏达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Aberdeen",
                            "city_code"  => "ABK",
                            "remark"     => "Aberdeen South Dakota,阿伯丁 南达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rapid City",
                            "city_code"  => "RAP",
                            "remark"     => "Rapid City South Dakota,拉皮德城 南达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sioux Falls",
                            "city_code"  => "FSD",
                            "remark"     => "Sioux Falls South Dakota,苏福尔斯 南达科他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "North Charleston",
                            "city_code"  => "NTS",
                            "remark"     => "North Charleston South Carolina,北查尔斯顿 南卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Charleston",
                            "city_code"  => "CHS",
                            "remark"     => "Charleston South Carolina,查尔斯顿 南卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Columbia",
                            "city_code"  => "COV",
                            "remark"     => "Columbia South Carolina,哥伦比亚 南卡罗来纳"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Omaha",
                            "city_code"  => "OMA",
                            "remark"     => "Omaha Nebraska,奥马哈 内布拉斯加"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bellevue",
                            "city_code"  => "XDE",
                            "remark"     => "Bellevue Nebraska,贝尔维尤 内布拉斯加"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Lincoln",
                            "city_code"  => "LNK",
                            "remark"     => "Lincoln Nebraska,林肯 内布拉斯加"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Elko",
                            "city_code"  => "EKO",
                            "remark"     => "Elko Nevada,埃尔科 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "North Las Vegas",
                            "city_code"  => "NVS",
                            "remark"     => "North Las Vegas Nevada,北拉斯维加斯 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Virginia City",
                            "city_code"  => "VGI",
                            "remark"     => "Virginia City Nevada,弗吉尼亚城 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Henderson",
                            "city_code"  => "HNZ",
                            "remark"     => "Henderson Nevada,亨德森 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Carson City",
                            "city_code"  => "CSN",
                            "remark"     => "Carson City Nevada,卡森城 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Las Vegas",
                            "city_code"  => "LAS",
                            "remark"     => "Las Vegas Nevada,拉斯维加斯 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Reno",
                            "city_code"  => "RNO",
                            "remark"     => "Reno Nevada,里诺 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sparks",
                            "city_code"  => "SPK",
                            "remark"     => "Sparks Nevada,斯帕克斯 内华达"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Buffalo",
                            "city_code"  => "FFO",
                            "remark"     => "Buffalo New York,布法罗 纽约"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rochester",
                            "city_code"  => "ROC",
                            "remark"     => "Rochester New York,罗切斯特 纽约"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "New York",
                            "city_code"  => "QEE",
                            "remark"     => "New York New York,纽约市 纽约"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Dover",
                            "city_code"  => "DOR",
                            "remark"     => "Dover Delaware,多佛 特拉华"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Newark",
                            "city_code"  => "NWK",
                            "remark"     => "Newark Delaware,纽瓦克 特拉华"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Wilmington",
                            "city_code"  => "ILG",
                            "remark"     => "Wilmington Delaware,威明顿 特拉华"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bristol",
                            "city_code"  => "BSJ",
                            "remark"     => "Bristol Tennessee,布利斯托 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Chattanooga",
                            "city_code"  => "CHA",
                            "remark"     => "Chattanooga Tennessee,查塔努加 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Kingsport",
                            "city_code"  => "TRI",
                            "remark"     => "Kingsport Tennessee,金斯波特 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Memphis",
                            "city_code"  => "MEM",
                            "remark"     => "Memphis Tennessee,孟菲斯 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Nashville",
                            "city_code"  => "BNA",
                            "remark"     => "Nashville Tennessee,纳什维尔 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Knoxville",
                            "city_code"  => "TYS",
                            "remark"     => "Knoxville Tennessee,诺克斯维尔 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tri-City Area",
                            "city_code"  => "YTC",
                            "remark"     => "Tri-City Area Tennessee,三城区 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Smyrna",
                            "city_code"  => "MQY",
                            "remark"     => "Smyrna Tennessee,士麦那 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Spring Hill",
                            "city_code"  => "RGI",
                            "remark"     => "Spring Hill Tennessee,斯普林希尔 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Johnson City",
                            "city_code"  => "JCY",
                            "remark"     => "Johnson City Tennessee,约翰逊城 田纳西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Appleton",
                            "city_code"  => "ATW",
                            "remark"     => "Appleton Wisconsin,阿普尓顿 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Oshkosh",
                            "city_code"  => "OSH",
                            "remark"     => "Oshkosh Wisconsin,奥什科什 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Green Bay",
                            "city_code"  => "GBK",
                            "remark"     => "Green Bay Wisconsin,格林贝 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Kenosha",
                            "city_code"  => "ENW",
                            "remark"     => "Kenosha Wisconsin,基诺沙 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "LaCrosse",
                            "city_code"  => "LSE",
                            "remark"     => "LaCrosse Wisconsin,拉克罗斯 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Racine",
                            "city_code"  => "RAC",
                            "remark"     => "Racine Wisconsin,拉辛 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Manitowoc",
                            "city_code"  => "MTW",
                            "remark"     => "Manitowoc Wisconsin,马尼托沃克 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Madison",
                            "city_code"  => "QMD",
                            "remark"     => "Madison Wisconsin,迈迪逊 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Milwaukee",
                            "city_code"  => "MKE",
                            "remark"     => "Milwaukee Wisconsin,密尔沃基 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Eau Claire",
                            "city_code"  => "EAU",
                            "remark"     => "Eau Claire Wisconsin,欧克莱尓 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Wausau",
                            "city_code"  => "AUW",
                            "remark"     => "Wausau Wisconsin,沃索 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Sheboygan",
                            "city_code"  => "SBM",
                            "remark"     => "Sheboygan Wisconsin,希博伊根 威斯康星"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Virginia Beach",
                            "city_code"  => "VAB",
                            "remark"     => "Virginia Beach Virginia,弗吉尼亚比奇 维吉尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Norfolk",
                            "city_code"  => "ORF",
                            "remark"     => "Norfolk Virginia,诺福克 维吉尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Chesapeake",
                            "city_code"  => "HTW",
                            "remark"     => "Chesapeake Virginia,切萨皮克 维吉尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Charleston",
                            "city_code"  => "CRW",
                            "remark"     => "Charleston West Virginia,查尔斯顿 西佛吉尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Huntington",
                            "city_code"  => "HNU",
                            "remark"     => "Huntington West Virginia,亨廷顿 西佛吉尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Parkersburg",
                            "city_code"  => "PKB",
                            "remark"     => "Parkersburg West Virginia,帕克斯堡 西佛吉尼亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Kailua",
                            "city_code"  => "KHH",
                            "remark"     => "Kailua Hawaii,凯卢阿 夏威夷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Honolulu",
                            "city_code"  => "HNL",
                            "remark"     => "Honolulu Hawaii,檀香山 夏威夷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Hilo",
                            "city_code"  => "ITO",
                            "remark"     => "Hilo Hawaii,希洛 夏威夷"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Concord",
                            "city_code"  => "CON",
                            "remark"     => "Concord New Hampshire,康科德 新罕布什尔"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Manchester",
                            "city_code"  => "MHT",
                            "remark"     => "Manchester New Hampshire,曼彻斯特 新罕布什尔"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Nashua",
                            "city_code"  => "ASH",
                            "remark"     => "Nashua New Hampshire,纳舒厄 新罕布什尔"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Albuquerque",
                            "city_code"  => "ABQ",
                            "remark"     => "Albuquerque New Mexico,阿尔伯克基 新墨西哥"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Las Cruces",
                            "city_code"  => "LRU",
                            "remark"     => "Las Cruces New Mexico,拉斯克鲁塞斯 新墨西哥"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Roswell",
                            "city_code"  => "ROW",
                            "remark"     => "Roswell New Mexico,罗斯韦尔 新墨西哥"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Santa Fe",
                            "city_code"  => "SAF",
                            "remark"     => "Santa Fe New Mexico,圣菲 新墨西哥"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Newark",
                            "city_code"  => "NRK",
                            "remark"     => "Newark New Jersey,纽瓦克 新泽西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Paterson",
                            "city_code"  => "PAT",
                            "remark"     => "Paterson New Jersey,帕特森 新泽西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Jersey City",
                            "city_code"  => "JEC",
                            "remark"     => "Jersey City New Jersey,泽西城 新泽西"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Phoenix",
                            "city_code"  => "PHX",
                            "remark"     => "Phoenix Arizona,凤凰城 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Glendale",
                            "city_code"  => "GDA",
                            "remark"     => "Glendale Arizona,格兰代尔 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Mesa",
                            "city_code"  => "MQA",
                            "remark"     => "Mesa Arizona,梅萨 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Scottsdale",
                            "city_code"  => "STZ",
                            "remark"     => "Scottsdale Arizona,史卡兹代尔 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tempe",
                            "city_code"  => "TPE",
                            "remark"     => "Tempe Arizona,坦普 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Tucson",
                            "city_code"  => "TUC",
                            "remark"     => "Tucson Arizona,图森 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Yuma",
                            "city_code"  => "YUM",
                            "remark"     => "Yuma Arizona,优玛 亚利桑那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Alton",
                            "city_code"  => "ALN",
                            "remark"     => "Alton Illinois,奥尔顿 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Aurora",
                            "city_code"  => "AUZ",
                            "remark"     => "Aurora Illinois,奥罗拉 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Bloomington",
                            "city_code"  => "BLO",
                            "remark"     => "Bloomington Illinois,布卢明顿 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Danville",
                            "city_code"  => "DVI",
                            "remark"     => "Danville Illinois,丹维尓 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "De Kalb",
                            "city_code"  => "DEK",
                            "remark"     => "De Kalb Illinois,迪卡尔布 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Decatur",
                            "city_code"  => "DEC",
                            "remark"     => "Decatur Illinois,迪凯持 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "East St Louis",
                            "city_code"  => "ESL",
                            "remark"     => "East St Louis Illinois,东圣路易斯 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Champaign-Urbana",
                            "city_code"  => "CMI",
                            "remark"     => "Champaign-Urbana Illinois,厄巴纳-香槟 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Galesburg",
                            "city_code"  => "GSU",
                            "remark"     => "Galesburg Illinois,盖尔斯堡 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Carbondale",
                            "city_code"  => "MDH",
                            "remark"     => "Carbondale Illinois,卡本代尔 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rock Island",
                            "city_code"  => "RKI",
                            "remark"     => "Rock Island Illinois,罗克艾兰 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Rockford",
                            "city_code"  => "RFD",
                            "remark"     => "Rockford Illinois,罗克福德 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Normal",
                            "city_code"  => "NOM",
                            "remark"     => "Normal Illinois,诺黙尔 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Peoria",
                            "city_code"  => "PLA",
                            "remark"     => "Peoria Illinois,皮奥里亚 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Centralia",
                            "city_code"  => "CRA",
                            "remark"     => "Centralia Illinois,森特勒利亚 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Springfield",
                            "city_code"  => "SPI",
                            "remark"     => "Springfield Illinois,斯普林菲尔德 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Waukegan",
                            "city_code"  => "UGN",
                            "remark"     => "Waukegan Illinois,沃其根 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Chicago",
                            "city_code"  => "CHI",
                            "remark"     => "Chicago Illinois,芝加哥 伊利诺斯"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Evansville",
                            "city_code"  => "EVV",
                            "remark"     => "Evansville Indiana,埃文斯维尔 印第安那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Fort Wayne",
                            "city_code"  => "FWA",
                            "remark"     => "Fort Wayne Indiana,韦恩堡 印第安那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Indianapolis",
                            "city_code"  => "IND",
                            "remark"     => "Indianapolis Indiana,印第安纳波利斯 印第安那"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Ogden",
                            "city_code"  => "OGD",
                            "remark"     => "Ogden Utah,奥格登 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Layton",
                            "city_code"  => "LTJ",
                            "remark"     => "Layton Utah,雷登 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Orem",
                            "city_code"  => "OEU",
                            "remark"     => "Orem Utah,欧仁 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Park City",
                            "city_code"  => "PAC",
                            "remark"     => "Park City Utah,帕克城 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Provo",
                            "city_code"  => "PVU",
                            "remark"     => "Provo Utah,普罗沃 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "St.George",
                            "city_code"  => "SGU",
                            "remark"     => "St.George Utah,圣乔治 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "West Valley City",
                            "city_code"  => "WVC",
                            "remark"     => "West Valley City Utah,西瓦利城 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Salt Lake City",
                            "city_code"  => "SLC",
                            "remark"     => "Salt Lake City Utah,盐湖城 犹他"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Augusta",
                            "city_code"  => "AUT",
                            "remark"     => "Augusta Georgia,奥古斯塔 佐治亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Columbus",
                            "city_code"  => "CZX",
                            "remark"     => "Columbus Georgia,哥伦布 佐治亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Macon",
                            "city_code"  => "MCN",
                            "remark"     => "Macon Georgia,梅肯 佐治亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Savannah",
                            "city_code"  => "SAV",
                            "remark"     => "Savannah Georgia,沙瓦纳 佐治亚"
                        ],
                        [
                            "country_id" => 3,
                            "currency"   => "USD",
                            "city"       => "Atlanta",
                            "city_code"  => "TAT",
                            "remark"     => "Atlanta Georgia,亚特兰大 佐治亚"
                        ]
                    ];
                    \App\Models\City::query()->insert($cities);
                    break;
                case 4:
                    $cities = [
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Amnat Charoen",
                            "city_code"  => "amnat charoen",
                            "remark"     => "安纳乍能"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Prachuap Khiri Khan",
                            "city_code"  => "prachuap khiri khan",
                            "remark"     => "巴蜀"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Pathum Thani",
                            "city_code"  => "pathum thani",
                            "remark"     => "巴吞他尼"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Prachin Buri",
                            "city_code"  => "prachin buri",
                            "remark"     => "巴真"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Kanchanaburi",
                            "city_code"  => "kanchanaburi",
                            "remark"     => "北碧"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Saraburi",
                            "city_code"  => "saraburi",
                            "remark"     => "北标"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Pattani",
                            "city_code"  => "pattani",
                            "remark"     => "北大年"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Samut Prakan",
                            "city_code"  => "samut prakan",
                            "remark"     => "北揽"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nakhon Sawan",
                            "city_code"  => "nakhon sawan",
                            "remark"     => "北榄坡"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chachoengsao",
                            "city_code"  => "chachoengsao",
                            "remark"     => "北柳"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phetchabun",
                            "city_code"  => "phetchabun",
                            "remark"     => "碧差汶"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phatthalung",
                            "city_code"  => "phatthalung",
                            "remark"     => "博达伦"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chai Nat",
                            "city_code"  => "chai nat",
                            "remark"     => "猜那"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chaiyaphum",
                            "city_code"  => "chaiyaphum",
                            "remark"     => "猜也奔"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Uttaradit",
                            "city_code"  => "uttaradit",
                            "remark"     => "程逸"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chumphon",
                            "city_code"  => "chumphon",
                            "remark"     => "春蓬"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chon Buri",
                            "city_code"  => "chon buri",
                            "remark"     => "春武里"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Tak",
                            "city_code"  => "tak",
                            "remark"     => "达"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Trat",
                            "city_code"  => "trat",
                            "remark"     => "达叻"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phra Nakhon Si Ayutthaya",
                            "city_code"  => "phra nakhon si ayutthaya",
                            "remark"     => "大城"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Trang",
                            "city_code"  => "trang",
                            "remark"     => "董里"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phetchaburi",
                            "city_code"  => "phetchaburi",
                            "remark"     => "佛丕"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nakhon Pathom",
                            "city_code"  => "nakhon pathom",
                            "remark"     => "佛统"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Kamphaeng Phet",
                            "city_code"  => "kamphaeng phet",
                            "remark"     => "甘烹碧"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Ang Thong",
                            "city_code"  => "ang thong",
                            "remark"     => "红统"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Lop Buri",
                            "city_code"  => "lop buri",
                            "remark"     => "华富里"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Kalasin",
                            "city_code"  => "kalasin",
                            "remark"     => "加拉信"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Krabi",
                            "city_code"  => "krabi",
                            "remark"     => "甲米"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chanthaburi",
                            "city_code"  => "chanthaburi",
                            "remark"     => "尖竹汶"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Khon Kaen",
                            "city_code"  => "khon kaen",
                            "remark"     => "孔敬"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Rayong",
                            "city_code"  => "rayong",
                            "remark"     => "拉农"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nong Khai",
                            "city_code"  => "nong khai",
                            "remark"     => "廊开"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nong Bua Lamphu",
                            "city_code"  => "nong bua lamphu",
                            "remark"     => "廊莫那浦"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Ratchaburi",
                            "city_code"  => "ratchaburi",
                            "remark"     => "叻丕"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Loei",
                            "city_code"  => "loei",
                            "remark"     => "黎"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Roi Et",
                            "city_code"  => "roi et",
                            "remark"     => "黎逸"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Samut Sakhon",
                            "city_code"  => "samut sakhon",
                            "remark"     => "龙仔厝"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Ranong",
                            "city_code"  => "ranong",
                            "remark"     => "罗勇"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nakhon Si Thammarat",
                            "city_code"  => "nakhon si thammarat",
                            "remark"     => "洛坤"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Maha Sarakham",
                            "city_code"  => "maha sarakham",
                            "remark"     => "玛哈沙拉堪"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Bangkok",
                            "city_code"  => "bangkok",
                            "remark"     => "曼谷"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Mukdahan",
                            "city_code"  => "mukdahan",
                            "remark"     => "莫达汉"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nakhon Nayok",
                            "city_code"  => "nakhon nayok",
                            "remark"     => "那空那育"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nakhon Phanom",
                            "city_code"  => "nakhon phanom",
                            "remark"     => "那空帕农"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nan",
                            "city_code"  => "nan",
                            "remark"     => "难"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Lamphun",
                            "city_code"  => "lamphun",
                            "remark"     => "南奔"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Nonthaburi",
                            "city_code"  => "nonthaburi",
                            "remark"     => "暖武里"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phrae",
                            "city_code"  => "phrae",
                            "remark"     => "帕"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phayao",
                            "city_code"  => "phayao",
                            "remark"     => "帕尧"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phangnga",
                            "city_code"  => "phangnga",
                            "remark"     => "攀牙"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phitsanulok",
                            "city_code"  => "phitsanulok",
                            "remark"     => "彭世洛"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phichit",
                            "city_code"  => "phichit",
                            "remark"     => "披集"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Phuket",
                            "city_code"  => "phuket",
                            "remark"     => "普吉"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chiang Rai",
                            "city_code"  => "chiang rai",
                            "remark"     => "清莱"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Chiang Mai",
                            "city_code"  => "chiang mai",
                            "remark"     => "清迈"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Sakon Nakhon",
                            "city_code"  => "sakon nakhon",
                            "remark"     => "色军"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Satun",
                            "city_code"  => "satun",
                            "remark"     => "沙敦"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Sa Kaeo",
                            "city_code"  => "sa kaeo",
                            "remark"     => "沙缴"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Si sa ket",
                            "city_code"  => "si sa ket",
                            "remark"     => "四色菊"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Songkhla",
                            "city_code"  => "songkhla",
                            "remark"     => "宋卡"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Sukhothai",
                            "city_code"  => "sukhothai",
                            "remark"     => "素可泰"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Surat Thani",
                            "city_code"  => "surat thani",
                            "remark"     => "素叻"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Surin",
                            "city_code"  => "surin",
                            "remark"     => "素林"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Suphan Buri",
                            "city_code"  => "suphan buri",
                            "remark"     => "素攀武里"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Narathiwat",
                            "city_code"  => "narathiwat",
                            "remark"     => "陶公"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Udon Thani",
                            "city_code"  => "udon thani",
                            "remark"     => "乌隆"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Uthai Thani",
                            "city_code"  => "uthai thani",
                            "remark"     => "乌泰他尼"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Ubon Ratchathani",
                            "city_code"  => "ubon ratchathani",
                            "remark"     => "乌汶"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Buri Ram",
                            "city_code"  => "buri ram",
                            "remark"     => "武里南"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Sing Buri",
                            "city_code"  => "sing buri",
                            "remark"     => "信武里"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Yasothon",
                            "city_code"  => "yasothon",
                            "remark"     => "耶梭通"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Yala",
                            "city_code"  => "yala",
                            "remark"     => "也拉"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Mae Hong Son",
                            "city_code"  => "mae hong son",
                            "remark"     => "夜丰颂"
                        ],
                        [
                            "country_id" => 4,
                            "currency"   => "THB",
                            "city"       => "Samut Songkhram",
                            "city_code"  => "samut songkhram",
                            "remark"     => "夜功"
                        ]
                    ];
                    \App\Models\City::query()->insert($cities);
                    break;
            }
        }
    }
}
