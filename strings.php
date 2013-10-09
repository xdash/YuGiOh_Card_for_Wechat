<?php

// 设置欢迎文案
define("WELCOME" , "欢迎关注游戏王微信卡片查询器！直接输入卡片关键词进行查询。输入 help 可以查看更多高级指令。本工具由 @XDash 开发。Enjoy it!");
define("SAYBYE" , "感谢使用游戏王微信卡片查询器。欢迎您再回来。");
define("NORESULT" , "没有搜索到相关卡片，请尝试其他关键词，或输入 help 查看全部指令。\n\n赞助我们：<a href='https://m.alipay.com/personal/payment.htm?userId=2088102797829218&reason=%E8%B5%9E%E5%8A%A9%E6%B8%B8%E6%88%8F%E7%8E%8B%E5%BE%AE%E4%BF%A1%E5%8D%A1%E6%9F%A5&weChat=true#wechat_redirect'>猛击这里</a>");

// 游戏王卡查根域名
define("OUROCG_ROOT" , "http://www.ourocg.cn/");
// 游戏王卡查API
define("OUROCG_API" , "http://www.ourocg.cn/Api/Search.aspx");
// 游戏王卡查WIKI页面
define("OUROCG_WIKI_PREFIX" , "http://www.ourocg.cn/Cards/Wiki-");
// 游戏王卡查图床地址前缀
define("OUROCG_PIC" , "http://p.ocgsoft.cn/");
// 自己的卡片图床地址
define("OWN_PIC_BED" , "http://www.syncoo.com/ourocg/cardpics/");
// 游戏王卡查单卡页面卡片前缀
define("OUROCG_MOBILE_PAGE" , "http://www.ourocg.cn/m/card-");
define("OUROCG_WEB_PAGE" , "http://www.ourocg.cn/Cards/View-");

// 游戏王卡查移动版转向页面
define("OUROCG_REDIRCT_MOBILE" , "redirect_mobile.php?cardid=");

//捐赠地址
define("DONATE_URL" , "https://m.alipay.com/personal/payment.htm?userId=2088102797829218&reason=%E8%B5%9E%E5%8A%A9%E6%B8%B8%E6%88%8F%E7%8E%8B%E5%BE%AE%E4%BF%A1%E5%8D%A1%E6%9F%A5&weChat=true#wechat_redirect"); 

// 设置默认图片 
define("DEFAULT_COVER", "");

// 卡片资料库总数量
define("TOTAL_CARD_COUNT", 6090);

// NWBBS游戏王板块
define("NWBBS_YUGIOH","http://www.nwbbs.com/forum.php?mod=forumdisplay&fid=8&mobile=yes&simpletype=no"); 
// NWBBS游戏王板块LOGO
define("NWBBS_LOGO","http://pic.newwise.com/forum/201212/11/201559dp22p2baipdd2bki.png"); 
// NWBBS游戏王板块根域名
define("NWBBS_HOST","http://www.nwbbs.com/"); 
//游戏王贴吧板块
define("TIEBA_YUGIOH","http://tieba.baidu.com/f?&mo_device=1&kw=游戏王"); 
// 游戏王贴吧LOGO
define("TIEBA_LOGO","http://pic.newwise.com/forum/201212/11/201559dp22p2baipdd2bki.png"); 
// 游戏王贴吧根域名
define("TIEBA_HOST","http://tieba.baidu.com"); 

// 查询来源网页 ourocg.cn(适用于抓取)
define("OUROCG","http://www.ourocg.cn/m/card-");
define("SEARCHPAGE","http://www.ourocg.cn/S.aspx?key=");
define("CARDPAGE","http://www.ourocg.cn/Cards/View-");

define("LatestCardList_Forbidden",$LatestCardList_Forbidden);
define("LatestCardList_Limit",$LatestCardList_Limit);
define("LatestCardList_Quasi_Limit",$LatestCardList_Quasi_Limit);
define("LatestCardList_No_Limit",$LatestCardList_No_Limit);
define("Advanced_Commands",$Advanced_Commands);

// 全部指令列表
define("ALLCOMMANDS" , "指令列表——\n\n【关键词】= 搜索卡片\n【r】= 随机抽一张卡片\n【rr】=随机抽中文卡\n【rrr】=随机抽日文卡\n【nw】= NWBBS新帖\n【bbs】= 论坛&贴吧\n【roll】= 抛骰子\n【coin】= 抛硬币\n【jz】= 禁止卡表\n【xz】= 限制卡表\n【zxz】= 准限制卡表\n【wxz】= 无限制卡表\n【yf】= 高级搜索语法\n【help】= 全部指令列表\n\n（更多指令开发中…）\n\n本游戏王微信卡查由 @XDash 开发，微信ID：ifanbing。\n\n官方QQ群：293512757\n\n赞助我们：<a href='https://m.alipay.com/personal/payment.htm?userId=2088102797829218&reason=%E8%B5%9E%E5%8A%A9%E6%B8%B8%E6%88%8F%E7%8E%8B%E5%BE%AE%E4%BF%A1%E5%8D%A1%E6%9F%A5&weChat=true#wechat_redirect'>猛击这里</a>"); 

// 高级指令
define("ADVANCED_COMMANDS","【高级搜索语法】\n\n语法概要：\n+() 里面的内容必须包含\n-() 里面的内容必须不包含\n\nAND(and) 两个条件同时成立\nOR(or)	 两个条件只要成立一个\n\n条件查询：(括号中用/分割的中任选一个即可，下面的范例就是从中选择一个做范例，实际上都可以使用) \n\n(中文名/卡名/name): 名称查询\n举例： 中文名:变形\n\n(日文名/japName): 日文名查询\n举例： japName:コー\n\n(简称/俗称/缩写/shortName):\n举例： 俗称:囧\n\n(卡种/卡片种类/cardType):卡种查询\n举例： cardType:魔法\n\n(种族/tribe):	 种族查询\n举例： 种族:龙\n\n(属性/element):\n举例： 属性:暗\n\n(卡包/package):\n举例： package:(606 or 605)\n\n(编号/序号/ID):\n举例： ID:(5 or 10)\n\n(攻/攻击力/atkValue):\n举例： 攻:500\n\n(防/防御力/defValue):\n举例： 防御力:500\n\n(星级/星数/等级/level):\n举例： 等级:5\n\nlimit:\n举例： limit:1\n\n数字还可以使用区间语法\n比如：\natkValue:400-500\n\n多条件查询举例：\n\n查询 战士族 4星 效果怪兽\n+(cardType:效果怪兽) +(tribe:战士) +(level:4)");

// 禁卡
define("FORBIDDEN_CARDS","【20130901 禁止卡】\n交换蛙\n胜利龙\n混沌帝龙 -终焉的使者-\n杀人蛇\n三眼怪 前限制\n成长的鳞茎\n黑森林的魔女\n御用守护者\n混沌之黑魔术师\n电子壶\n千眼纳祭神\n处刑人-摩休罗\n神圣魔术师\n发条空母 发条巨舰 前限制\n暗黑俯冲轰炸机\n命运英雄 圆盘人\n恶魔 弗兰肯\n同族感染病毒\n冰结界之龙 光枪龙\n纤维壶\n电子鱼人-枪手\n魔导科学家\n精神脑魔\n八汰乌\n救援猫\n噩梦之蜃气楼\n爱恶作剧的双子恶魔\n王家的神殿\n收押\n苦涩的选择\n强引的番兵\n强夺\n强欲之壶\n心变\n雷击\n次元融合\n生还的宝札\n洗脑\n大寒波\n蝶之短剑-回音\n天使的施舍\n鹰身女妖的羽毛扫\n过早的埋葬\n飓风\n质量加速器\n未来融合\n突然变异\n遗言状\n王宫的弹压\n王宫的敕命\n现世与冥界的逆转\n死之卡组破坏病毒\n第六感\n滑槽\n刻之封印\n破坏轮\n最终一战！\n炎征龙-燃龙\n水征龙-流龙\n地征龙-迹龙\n风征龙-霆龙\n魔导书的神判");

// 限制卡
define("LIMIT_CARDS","【20130901 限制卡】\n邪遗式幽风乌贼怪\n甲虫装机 豆娘\n甲虫装机 大黄蜂\n元素英雄 天空侠\n欧尼斯特\n混沌战士 -开辟的使者-\n真六武众-紫炎\n僵尸带菌者\n暗黑武装龙\n蒲公英狮\n科技属 突击兵\n科技属 超图书馆员\n深渊的暗杀者\n死灵之颜\n被封印的艾克佐迪亚\n被封印者的右足\n被封印者的右腕\n被封印者的左足\n被封印者的左腕\n方程式同调士\n黑羽-疾风之盖尔\n马头鬼\n变形壶\n真红眼暗铁龙\n孤火花\n来自异次元的埋葬\n一时休战 前无限制\n永火炮\n大风暴\n愚蠢的埋葬\n原初之种\n死者苏生\n精神操作\n增援\n月之书\n手札抹杀\n贪欲之壶\n光之援军\n黑洞\n怪兽之门\n暗之诱惑\n限制解除\n六武之门\n一对一\n来自异次元的归还\n神之警告 前准限制\n神之宣告\n血之代偿\n停战协定\n转生的预言\n光之护封壁\n魔力爆发\n盟军·次世代鸟人\n发条鲨\nNo.11 巨眼\n冰结界之龙 三叉龙\n水精鳞-邓氏深渊鱼\n立炎星-董鸡\n超再生能力\n霞之谷的神风\n深渊死球");

// 准限制卡
define("QUASI_LIMIT_CARDS","【20130901 准限制卡】\n卡片炮击士\n召唤僧\n神秘之代行者 厄斯\n大天使 克里斯提亚\n命运英雄 魔性人\n星骸龙\n特拉戈迪亚\n冰结界的虎王 雪虎\n由魔界到现世的死亡导游\n雷王 前无限制\n轮回天狗\n救援兔\n王家的牲祭\n召集之圣刻印\n连锁爆击\n英雄到来\n魔法石采掘\n扰乱三人组\n激流葬\n奈落的落穴\n混沌巫师\n剑斗兽 枪斗\n新宇宙侠·大地鼹鼠\n冥府之使者 格斯\n炎舞-「天玑」\n黑旋风");

// 无限制卡
define("NO_LIMIT_CARDS","【20130901 无限制卡】\n发条魔术师\n月读命\nE-紧急呼唤\n高等仪式术\n强欲而谦虚之壶\n替罪羊\n名推理\n神圣防护罩-反射镜力-");




?>
