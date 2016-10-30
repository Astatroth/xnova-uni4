<?php

if (!defined('INSIDE')) {
	die("attemp hacking");
}

// ----------------------------------------------------------------------------------------------------------

$lang['nfo_page_title']  = "Информация";
$lang['nfo_title_head']  = "Информация";
$lang['nfo_name']        = "Имя";
$lang['nfo_destroy']     = "Снести";
$lang['nfo_level']       = "Уровень";
$lang['nfo_range']       = "Дальность действия сенсоров";
$lang['nfo_used_energy'] = "Потребление энергии";
$lang['nfo_used_deuter'] = "Потребление дейтерия";
$lang['nfo_prod_energy'] = "Производство энергии";
$lang['nfo_difference']  = "Разница";
$lang['nfo_prod_p_hour'] = "Факор продукции";
$lang['nfo_needed']      = "Требуется";
$lang['nfo_dest_durati'] = "Время сноса";
$lang['nfo_rf_again']    = "Одним залпом поражает";
$lang['nfo_rf_from']     = "одним залпом поражает";

// ----------------------------------------------------------------------------------------------------------

$lang['gate_start_moon'] = "Исходная точка отправления";
$lang['gate_dest_moon']  = "Пункт назначения";
$lang['gate_use_gate']   = "Использовать Межгалактические врата";
$lang['gate_ship_sel']   = "Количество кораблей";
$lang['gate_ship_dispo'] = "В распоряжении";
$lang['gate_jump_btn']   = "Погрузка";
$lang['gate_jump_done']  = "Погрузка завершенна, теперь скачок возможен: ";
$lang['gate_wait_dest']  = "Энергия ворот цели еще не соединилась с вашей: ";
$lang['gate_no_dest_g']  = "На эту планету не ведут ворота!";
$lang['gate_wait_star']  = "Энергия ворот израсходована, время до перезарядки ";
$lang['gate_wait_data']  = "Ошибка, ошибочные данные!";

// ----------------------------------------------------------------------------------------------------------

$lang['info'][1] = 'Основной поставщик сырья для строительства несущих структур построек и кораблей. Металл - самое дешёвое сырьё, но зато его требуется больше, чем всего остального. Для производства металла требуется меньше всего энергии. Чем рудники больше, тем они глубже. На большинстве планет металл находится на больших глубинах, при помощи этих более глубоких рудников можно добывать больше металлов, производство растёт. В то же время более крупные рудники требуют больше энергии.';
$lang['info'][2] = 'Основной поставщик сырья для электронных строительных элементов и сплавов. Для добычи кристалла требуется примерно вдвое больше энергии, чем для добычи металла, поэтому он, соответственно, ценится больше. Кристалл требуется для всех кораблей и зданий. К сожалению, большинство необходимых для строительства кораблей кристаллов очень редки и, как и большинство металлов, залегают на больших глубинах. Поэтому при усовершенствовании рудника также повышается производство, так как осваиваются более крупные и "чистые" залежи кристаллов.';
$lang['info'][3] = 'Дейтерий - это тяжёлый водород. Из-за этого, как и на рудниках, более крупные запасы находятся на дне моря. Усовершенствование синтезатора также способствует освоению этих глубинных залежей дейтерия. Дейтерий необходим в качестве топлива для кораблей, почти для всех исследований, просмотра галактик, а также для использования сенсорной фаланги.';

$lang['info'][4] = 'Для обеспечения энергией рудников и синтезаторов необходимы огромные солнечные электростанции. Чем больше построено станций, тем больше поверхности покрыто кварцевыми пластинами, которые перерабатывают световую энергию в электроэнергию. Солнечные электростанции представляют собой основу энергообеспечения планеты.';
$lang['info'][12] = 'На термоядерных электростанциях при помощи холодного термоядерного синтеза под огромным давлением и при большой температуре 2 атома тяжёлого водорода объединяются в один атом гелия. При этом, при образовании ядра гелия, вырабатывается энергия в 41,32*10^-13 джоулей в виде излучения (т.о. при сгорании 1 г водорода вырабатывается 172 мВт/ч энергии). Чем больше термоядерный реактор, тем сложнее процессы синтезирования, реактор производит больше энергии.';

$lang['info'][14] = 'Предоставляет простую рабочую силу, которую можно применять при строительстве планетарной инфраструктуры. Каждый уровень развития фабрики повышает скорость строительства зданий.';
$lang['info'][15] = 'Фабрика нанитов представляет собой венец робототехники. Наниты - это роботы размером в нанометр, которые путём объединения в сеть в состоянии выполнять экстраординарные задания. Сразу же после исследования они увеличивают производительность почти во всех областях. С каждым уровнем фабрика нанитов сокращает время строительства зданий, кораблей и оборонительных сооружений вдвое.';
$lang['info'][21] = 'В строительной верфи производятся все виды кораблей и оборонительных сооружений. Чем она больше, тем быстрее можно строить более сложные и более крупные корабли и оборонительные сооружения. Посредством строительства фабрики нанитов производятся миниатюрные роботы, которые помогают работникам работать быстрее.';
$lang['info'][22] = 'Огромное хранилище для добытых руд. Чем оно больше, тем больше металла можно в нём хранить. Если оно заполнено, то добыча металла прекращается.';
$lang['info'][23] = 'В этом огромном хранилище складируется ещё не обработанный кристалл. Чем хранилище больше, тем больше кристалла там можно хранить. Если оно заполнено, то добыча сего ресурса прекращается.';
$lang['info'][24] = 'Огромные ёмкости для хранения добытого дейтерия. Они обычно находятся вблизи космических портов. Чем они больше, тем больше дейтерия в них может сберегаться. Если они заполнены, то добыча дейтерия прекращается.';
$lang['info'][31] = 'Для исследования новых технологий необходима работа исследовательской станции. Уровень развития исследовательской станции является решающим фактором того, как быстро могут быть освоены новые технологии. Чем выше уровень развития исследовательской лаборатории, тем больше может быть исследовано новых технологий. Для того, чтобы как можно быстрее завершить исследовательские работы на одной планете, на неё посылаются все имеющиеся в наличии учёные и, таким образом, покидают свои планеты. Как только технология исследована, учёные возвращаются на свои родные планеты и переносят с собой знания о ней. Так новые технологии можно применять и на других планетах.';
$lang['info'][33] = 'По мере застройки планет всё более важным становился вопрос об ограниченности пригодных для использования площадей. Такие традиционные методы, как строительство ввысь и вглубь, оказались недостаточными. Маленькая группа физиков и нанотехников нашла решение - терраформер. Затрачивая огромное количество энергии терраформер может преобразовывать огромные территории и даже целые континенты, делая их пригодными для застройки. В этом строении беспрерывно производятся специальные наниты, отвечающие за постоянное качество почвы.';
$lang['info'][34] = 'Склад альянса предоставляет возможность обеспечения топливом дружественных флотов, которые помогают при обороне и находятся на орбите. С каждым уровнем развития можно посылать флотам на орбите дополнительно 10 000 единиц дейтерия в час.';

$lang['info'][41] = 'Луна не располагает атмосферой, поэтому перед заселением требуется соорудить лунную базу. Она обеспечивает необходимые воздух, гравитацию и тепло. Чем выше уровень развития лунной базы, тем больше обеспеченная биосферой площадь. Каждый уровень лунной базы может застроить 3 поля, максимум до площади всей луны. Она составляет 2 (диаметр луны/1000), причём каждый уровень лунной базы сам занимает одно поле.';
$lang['info'][42] = 'Высокочастотные сенсоры полностью просматривают спектр частот всех попадающих на фалангу излучений. Мощные компьютеры комбинируют мельчайшие колебания энергии и таким образом получают информацию о передвижениях кораблей на отдалённых планетах. Для просмотра на луне должна быть предоставлена энергия в форме дейтерия (5 000 за просмотр). Просмотр осуществляется переходом с луны в меню "Галактика" и на название вражеской планеты, находящейся в радиусе действия сенсоров (формула: (уровень фаланги)^2-1 систем).';
$lang['info'][43] = 'Ворота - это огромные телепортеры, которые могут пересылать между собой флоты любых размеров без временных затрат. Эти телепортеры не требуют дейтерия, однако между двумя прыжками должен пройти один час, иначе ворота перегреются. Невозможна также пересылка ресурсов. Весь процесс требует крайне высоко развитой технологии.';
$lang['info'][44] = 'Ракетные шахты служат для хранения ракет. С каждым уровнем можно хранить на пять межпланетных или десять ракет-перехватчиков больше. Одна межпланетная ракета требует места в два раза больше, чем ракета-перехватчик. Возможно любое комбинирование различных типов ракет.';

$lang['info'][106] = 'Шпионаж предназначен для исследования новых и более эффективных сенсоров. Чем выше развита эта технология, тем больше информации имеет игрок о событиях в своём окружении. Разница в уровнях шпионажа с противником играет решающую роль - чем больше исследована собственная шпионская технология, тем больше информации содержится в разведданных и тем меньше шанс быть обнаруженным. Чем больше послано зондов, тем больше собирается подробностей о противнике, но при этом растёт опасность быть обнаруженным. Также шпионаж совершенствует определение местонахождения чужих флотов. При этом также важен уровень развития собственного шпионажа. Начиная со 2 уровня его развития при атаке на Вас кроме сообщения о нападении показывается также и общая численность нападающих кораблей. С 4 уровня распознаётся вид нападающих кораблей, равно как и их общая численность, а с 8 - точная численность каждого типа кораблей. Для налётчиков эта технология очень важна, так как она предоставляет информацию о том, выставила ли жертва флот и/или защиту или нет, поэтому следует исследовать её как можно раньше. Лучше всего - сразу же после исследования малых транспортов.';
$lang['info'][108] = 'Компьютерная технология предназначена для расширения имеющихся в наличии компьютерных мощностей. В результате на планете развиваются более продуктивные и эффективные компьютерные системы, возрастает вычислительная мощность и скорость протекания вычислительных процессов. С повышением мощности компьютеров можно одновременно командовать всё бoльшим количеством флотов. Каждый уровень развития компьютерной технологии даёт возможность командовать +1 флотом. Чем больше рассылается флотов, тем больше можно совершать налётов и тем самым захватывать больше сырья. Естественно, что эта технология полезна и торговцам, так как она позволяет им одновременно рассылать больше торговых флотов. По этой причине следует постоянно развивать компьютерную технологию на протяжении всей игры.';
$lang['info'][109] = 'Оружейная технология занимается прежде всего дальнейшим развитием имеющихся в наличии систем вооружения. При этом особое значение придаётся тому, чтобы снабжать имеющиеся в наличии системы большей энергией и более точно эту энергию направлять. Благодаря этому системы вооружения становятся эффективней, а оружие вызывает больше разрушений. Каждый уровень оружейной технологии увеличивает мощность вооружения войсковых частей на 5%. Оружейная технология важна для конкурентоспособного содержания частей. Поэтому следовало бы её постоянно развивать в течение всей игры.';
$lang['info'][110] = 'Эта технология занимается изучением более новых возможностей большего энергоснабжения щитов, что делает их эффективней и устойчивей. Благодаря этому с каждым изученным уровнем эффективность щитов повышается на 3%.';
$lang['info'][111] = 'Специальные сплавы улучшают броню космических кораблей. Как только найден очень стойкий сплав, специальные лучи изменяют молекулярную структуру космического корабля, и доводит её до состояния изученного сплава. Так, устойчивость брони может увеличиваться с каждым уровнем на 5%.';
$lang['info'][113] = 'Энергетическая технология занимается дальнейшим развитием систем передачи и хранения энергии, которые необходимы для многих новых технологий.';
$lang['info'][114] = 'Путём сплетения 4-го и 5-го измерения стало возможным исследовать новый более экономный и эффективный двигатель.';
$lang['info'][115] = 'В основе реактивного двигателя лежит закон сохранения импульса. Материя, разогретая до высоких температур, выбрасывается в направлении, противоположном движению и даёт ускорение кораблю. Эффективность этих двигателей достаточно мала, но они достаточно надёжны, дёшевы в производстве и обслуживании. Кроме того они занимают гораздо меньше места на корабле по сравнению с остальными двигателями, поэтому их всё ещё достаточно часто можно встретить на маленьких кораблях. Так как реактивные двигатели являются основой любого полёта в космос, следует исследовать их как можно раньше. Дальнейшее развитие этих двигателей делает следующие корабли с каждым уровнем на 10% быстрее: малые и большие транспорты, лёгкие истребители и шпионские зонды.';
$lang['info'][117] = 'Импульсный двигатель основывается на принципе отдачи, при котором возникает масса лучей, по большей части в виде побочного продукта ядерного синтеза, использованного для получения энергии. Также можно впрыснуть дополнительную массу. Дальнейшее развитие этих двигателей делает следующие корабли с каждым уровнем на 20% быстрее: бомбардировщики, крейсеры, тяжёлые истребители и колонизаторы. Каждый уровень развития увеличивает радиус действия межпланетных ракет.';
$lang['info'][118] = 'Благодаря пространственно-временному изгибу в непосредственном окружении корабля пространство сжимается, чем быстрее преодолеваются далёкие расстояния. Чем выше развит гиперпространственный привод, тем выше сжатие пространства, благодаря чему с каждым уровнем скорость кораблей повышается на 30%.';
$lang['info'][120] = 'Лазеры (усиление света при помощи индуцированного выброса излучения) производят насыщенный энергетический луч когерентного света. Эти приборы находят применение во всевозможных областях, от оптических компьютеров до тяжёлых лазеров, которые свободно режут броню космических кораблей. Лазерная технология является важным элементом для исследования дальнейших оружейных технологий.';
$lang['info'][121] = 'Поистине смертоносный наводимый луч из ускоренных ионов. При попадании на какой-либо объект они наносят огромный ущерб.';
$lang['info'][122] = 'Дальнейшее развитие ионной технологии, которая ускоряет не ионы, а высокоэнергетическую плазму. Она оказывает опустошительное действие при попадании на какой-либо объект.';
$lang['info'][123] = 'Эта сеть делает возможным общение учёных, работающих в исследовательских лабораториях разных планет. Каждый новый уровень позволяет присоединить к сети дополнительную лабораторию (в первую очередь присоединяются лаборатории старших уровней). Из всех объединённых в сеть лабораторий, в каждом исследовании принимают участие только те, которые имеют достаточный для проведения данного исследования уровень. Скорость исследования соответствует сумме уровней участвующих в нём лабораторий.';
$lang['info'][124] = "Экспедиционная технология охватывает различные технологии сканирования и даёт возможность оснащать корабли различных классов исследовательским модулем. Он содержит базу данных, маленькую передвижную лабораторию, а также различные биоклетки и сосуды для проб. Для безопасности корабля при исследовании опасных объектов исследовательский модуль оснащён автономным энергообеспечением и генератором энергетического поля, который в экстремальных ситуациях может окружать исследовательский модуль мощным энергетическим полем.";
$lang['info'][150] = "Император, имеющий много колоний во вселеной имеет больше преимуществ перед другими. Каждый уровень развития данной технологии увеличивает максимальное количество планет вашей империи на +1.";
$lang['info'][161] 	= "Обойдя ограничение на количество планет, вы сможете создавать планеты-карлики, которые удобно использовать в качестве промежуточных баз для флота. Максимальная возможность застройки данной территории - 10 полей. Что на ней размещать это решать вам. С каждым уровнем развития данной технологии увеличивается максимальное количество таких баз.";
$lang['info'][199] 	= 'Гравитон - это частица, которая не обладает ни массой ни зарядом и определяет силу притяжения. Путём запуска концентрированного заряда гравитонов можно создавать искусственное гравитационное поле, которое, подобно чёрной дыре, втягивает в себя массу, благодаря чему можно уничтожать корабли или даже луны. Чтобы произвести достаточное количество гравитонов, требуются огромные количества энергии.';

$lang['info'][202] = 'Транспорты имеют примерно такой же размер, что и истребители, но они не обладают мощными двигателями и бортовым вооружением ради экономии места. Малый транспорт вмещает 5000 единиц сырья. По причине малой огневой мощи малые транспорты часто сопровождаются другими кораблями. Когда импульсный двигатель исследован до 5-й ступени, у малого транспорта повышается базовая скорость и он оснащается этим типом двигателя.	';
$lang['info'][203] = 'У этого корабля нет ни вооружения, ни каких-либо других технологий на борту. По этой причине никогда не следует запускать их без спровождения. Благодаря своему высокоразвитому двигателю внутреннего сгорания большой транспорт служит в качестве быстрого межпланетного доставщика ресурсов, также он сопровождает флоты при нападениях на вражеские планеты, чтобы захватить как можно больше ресурсов.';
$lang['info'][204] = 'Лёгкий истребитель - это манёвренный корабль, который можно найти почти на каждой планете. Затраты на нем не особо велики, однако щитовая мощность и вместимость очень малы.	';
$lang['info'][205] = 'При дальнейшем развитии лёгкого истребителя учёные дошли до момента, когда стало ясно, что обыкновенный двигатель не обладает необходимой мощью. Для того, чтобы оптимально передвигать корабль был впервые использован импульсный двигатель. Хоть он и повысил стоимость, однако он также открыл новые возможности. Благодаря применению этого двигателя осталось больше энергии для вооружения и щитов, кроме того, для этого вида истребителей также использовались ценные материалы. Это привело к улучшенной структурной целостности и более сильной огневой мощи, благодаря чему в бою он представляет бoльшую угрозу, чем его предшественник. После этих изменений тяжёлый истребитель представляет собой новую эру технологии кораблестроения, основу технологии крейсеростроения.';
$lang['info'][206] = 'С развитием тяжёлых лазеров и ионных пушек тяжёлые истребители всё больше вытеснялись. Несмотря на многочисленные усовершенствования огневая мощь и бронирование не могли быть настолько изменены, чтобы действенно противостоять этим оборонительным орудиям. Поэтому было решено построить новый класс кораблей, который объединял бы в себе больше бронирования и огневой мощи. Так появился крейсер. Крейсеры почти втрое сильней защищены, чем тяжёлые истребители и обладают более чем удвоенной огневой мощью. К тому же они очень быстры. Нет лучшего оружия против средней защиты. Почти столетие крейсеры неограниченно господствовали во вселенной. С появлением орудий Гаусса и плазменных орудий их господство закончилось. Однако и сегодня их охотно применяют против групп истребителей.';
$lang['info'][207] = 'Линкоры как правило составляют основу флота. Их тяжёлые орудия, высокая скорость и большой грузовой тоннаж делают их серьёзными противниками.';
$lang['info'][208] = 'Этот хорошо защищённый корабль служит покорению новых планет, что необходимо развивающейся империи. Он используется в новой колонии в качестве поставщика сырья - его разбирают и используют весь полезный материал для освоения нового света. Каждая империя, включая главную планету, может колонизировать максимум 9 планет.';
$lang['info'][209] = 'Космические бои принимали всё бoльшие масштабы. Уничтожались тысячи кораблей и возникавшие при этом обломки казались навсегда потерянными. Нормальные транспорты не могли близко к ним приблизиться, не будучи сильно повреждёнными маленькими обломками. С новым открытием в области щитовой технологии стало возможно эффективно устранять эту проблему, возник новый класс корабля, подобный большому транспорту - переработчик. С его помощью можно было заново использовать казавшиеся потерянными ресурсы. Из-за новых щитов маленькие обломки больше не представляли собой опасности. К сожалению, эти устройства требуют пространства, поэтому его грузовой тоннаж ограничен до 20 000.';
$lang['info'][210] = 'Шпионские зонды - это маленькие манёвренные корабли, которые доставляют с больших расстояний данные о флотах и планетах. Их высокомощный двигатель позволяет им преодолевать большие расстояния за несколько секунд. Однажды попав на орбиту какой-нибудь планеты, они пребывают там некоторое время для сборки данных. В это время враг может их относительно легко обнаружить и атаковать. Для экономии места не было установлено ни брони, ни щитов, ни орудий, что делает зонды в случае обнаружения лёгкими целями.';
$lang['info'][211] = 'Бомбардировщик был разработан специально для того, чтобы уничтожать планетарную защиту. С помощью лазерного прицела он точно сбрасывает плазменные бомбы на поверхность планеты и таким образом наносит огромные повреждения оборонительным сооружениям. Когда гиперпространственный двигатель исследован до 8-й ступени, у бомбардировщика повышается базовая скорость и он оснащается этим типом двигателя.';
$lang['info'][212] = 'Солнечные спутники запускаются на орбиту планеты. Они фокусируют солнечную энергию и передают её на наземную станцию. Эффективность солнечных спутников зависит от мощи солнечного излучения. В принципе, добыча энергии на орбитах, более приближённых к солнцу, выше, чем на планетах, удалённых от солнца. Из-за своего соотношения цены и качества солнечные спутники решают энергетические проблемы многих миров. Но внимание: солнечные спутники могут быть уничтожены в бою.';
$lang['info'][213] = 'Уничтожитель - король среди военных кораблей. Его мультифланговые ионные, плазменные и гауссовые орудийные башни могут благодаря своим усовершенствованным пеленгационным сенсорам поражать с точностью до 99% даже скоростные манёвренные истребители. Так как уничтожители очень велики, их манёвренность очень ограничена, и в бою они подобны скорее боевой станции, чем боевому кораблю. Потребление дейтерия у них так же высоко, как и их боевая мощь.';
$lang['info'][214] = 'Звезда смерти оснащена гравитонной пушкой, которая может уничтожать такие корабли, как уничтожитель, и даже луны. Так как для этого требуется большое количество энергии, она состоит почти лишь из генераторов. Только огромные звёздные империи могут вообще предоставить ресурсы и работников, чтобы построить этот размером с луну корабль.';
$lang['info'][215] = 'Этот корабль был специально сооружен для сражений с более мощными флотилиями.<br>Его усовершенствованные лазерные орудия в состоянии разбить большое количество атакующих кораблей одновременно. <br>Из-за его небольших размеров и мощного вооружения он ограничен в зарядной емкости, но зато гиперпространственный двигатель потребляет малое количество топлива.';

$lang['info'][216] = 'Этот высокотехнологичный корабль, основанный на синтезе тёмной материи способен расширить ваши владения во вселенной, благодаря иследованию ТМ вы можете превратить любую незаселённую территорию в своего рода планету-карлик, где вы сможете создать промежуточную базу для вашего флота. Из недостатков данной технологии можно отнести малое количество полей для застройки.';

$lang['info'][220] = 'Многоцелевой боевой быстроходный маневренный корабль Конфедерации для борьбы с истребителями и штурмовиками, охраны и обороны соединений кораблей. Используется также для разведывательной и дозорной служб.';
$lang['info'][221] = 'Только бионикам с их высочайшим уровнем развития технологий удалось извлечь из "устаревших" импульсных двигателей максимум мощностей. Думаете, что гиперпространственный двигатель дает вам весомое преимущество? Ваши надежды быстро развеются, когда вы хоть раз увидите, с какой скоростью передвигается перехватчик. Обладая хорошим для своего класса кораблей вооружением, группа перехватчиков может очень успешно терроризировать развивающиеся колонии противников.';
$lang['info'][222] = 'Сайлоны никогда не стремились воевать. Однако постоянные набеги на их благополучно развивающиеся планеты вынудили роботов создать новый тип военных кораблей, управляемых искусственным интеллектом. И пусть вас не вводит в заблуждение их массивный и неуклюжий вид: те, кто встретился с дредноутом в бою, знают, что пощады не будет.';
$lang['info'][223] = 'Древние известны во всех уголках галактики как самые жестокие и удачливые пираты. Хотя их удача - это скорее сказки для доверчивых детей. Успешность большинства вылазок древних обусловлена наличием специальных кораблей, созданных по схемам тяжелых истребителей. Древним удалось наладить симбиоз между структурой кораблей и собственными телами. За счет этого управление кораблем стало интуитивным, грузоподъемность значительно увеличилась, так как появилась возможность избавиться от бортовых компьютеров и прочих ненужных вещей, а фермент, выделяемый телом древних, уменьшил расход топлива.';

$lang['info'][401] = 'Ракетная установка - простое и дешёвое средство обороны. Так как это развитие обычных баллистических орудий, то ему не требуется дальнейшей модернизации. Малые затраты на его производство оправдывают его применение против более маленьких флотов, но со временем он теряет значение. Позднее его используют лишь для отвода вражеских выстрелов. Оборонительные сооружения деактивируются сами по себе, как только они сильно повреждаются. Возможность восстановления оборонительных сооружений после боя составляет до 70%.';
$lang['info'][402] = 'Для компенсации чрезмерных успехов в области технологии космических кораблей учёные должны были создать оборонительное сооружение, справляющееся с более крупными и лучше вооружёнными флотами. Это привело к появлению рождение лёгкого лазера. При помощи концентрированного обстрела цели фотонами можно достичь значительно бoльших разрушений, чем при применении обычного баллистического вооружения. Для противостояния более сильной огневой мощи новых типов кораблей он также оснащён усовершенствованными щитами. Однако, чтобы стоимость производства оставалась низкой, структура дальше не усиливалась. Лёгкий лазер обладает наилучшим соотношением цены и качества, поэтому он также интересен и для более развитых цивилизаций. Оборонительные сооружения деактивируются сами по себе, как только они сильно повреждаются. Возможность восстановления оборонительных сооружений после боя составляет до 70%.';
$lang['info'][403] = 'Тяжёлый лазер представляет собой дальнейшее развитие лёгкого лазера. Структура была усилена и усовершенствована новыми материалами. Оболочку смогли сделать значительно более стойкой. Одновременно была улучшена и энергетическая система и целевой компьютер, так что тяжёлый лазер может концентрировать значительно больше энергии на цели. Оборонительные сооружения деактивируются сами по себе, как только они сильно повреждаются. Возможность восстановления оборонительных сооружений после боя составляет до 70%.';
$lang['info'][404] = 'Долгое время артиллерийские орудия считались устаревшими по сравнению с современной ядерной и энергетической техникой, развитием гиперпространственного привода и постоянно улучшающимися бронировками, пока именно энергетическая техника, которая их когда-то оттесняла, не помогла им занять их исконное место. Вообще-то принцип ускорителя частиц был известен на Земле уже с 20-го и 21-го столетий. Пушка Гаусса - это ничто иное, как значительно бoльшая версия этой конструкции. Многотонные заряды магнетически ускоряются при огромных затратах энергии и имеют такую выходную скорость, что частички грязи в воздухе вокруг заряда сгорают, а отдача сотрясает землю. Даже современные бронировки и щиты могут с трудом противостоять этой пробивной силе, и нередко случается так, что цель просто простреливается насквозь. Оборонительные сооружения деактивируются сами по себе, как только они сильно повреждаются. Возможность восстановления оборонительных сооружений после боя составляет до 70%.';
$lang['info'][405] = 'В 21-м столетии на Земле уже существовало то, что общеизвестно как ЭМИ. ЭМИ означает электромагнитный импульс, который обладает способностью индуцировать дополнительные напряжения в схемы и тем самым причинять массовые помехи, которые могут уничтожить все чувствительные приборы. Тогда ЭМИ-орудия были в основном на базе ракет и бомб, также в комбинации с ядерными орудиями. Между тем ЭМИ постоянно развивался, так как в нём видели большой потенциал не уничтожать цели, а делать их неспособными к бою и манёвренности и, тем самым, упрощать их захват. Пока что наивысшая форма ЭМИ-орудий представлена ионным орудием. Оно направляет на цель волну ионов (электрически заряженных частиц), которая дестабилизирует щиты и повреждает электронику, только если она не очень хорошо защищена, что иногда подобно полному уничтожению. Кинетической пробивной силой можно пренебречь. Ионная техника используется только на крейсерах, так как потребление энергии ионными орудиями огромно, и в бою часто приходится уничтожить, а не парализовать цель. Оборонительные сооружения деактивируются сами по себе, как только они сильно повреждаются. Возможность восстановления оборонительных сооружений после боя составляет до 70%.';
$lang['info'][406] = 'Лазерная технология была доведена до совершенства, ионная техника достигла конечной стадии и считалось, что практически невозможно, даже с качественной точки зрения орудийной системы, достичь ещё больше эффективности. Но всё должно было измениться, когда появилась идея объединить обе системы. Из термоядерной технологии известно, что лазеры нагревают частицы (обычно дейтерия) до крайне высоких температур, исчисляемых в миллионах градусов. Ионная техника обогащает их электрическим зарядом, стабилизационными полями и ускорителями. Как только заряд достаточно нагрет, ионизирован и находится под давлением, то его выпускают посредством ускорителей во вселенские дали в направлении цели. Светящийся голубоватым цветом плазменный шар выглядит внушительно, только спрашивается, долго ли им будет наслаждаться команда корабля-цели, если через несколько секунд броня разорвётся на куски, а электроника сгорит... Плазменное орудие считается вообще самым страшным оружием, и у этой техники есть своя цена. Оборонительные сооружения деактивируются сами по себе, как только они сильно повреждаются. Возможность восстановления оборонительных сооружений после боя составляет до 70%.';
$lang['info'][407] = 'Задолго до того, как щитовые генераторы были достаточно малы, чтобы найти применение на кораблях, уже существовали огромные генераторы на поверхности планет. Они обволакивали целую планету силовым полем, которое могло поглощать удары атаки. Малые атакующие флоты постоянно разбиваются об эти щитовые купола. Благодаря растущему технологическому развитию эти щиты можно ещё усилить. Позже можно строить более сильный большой щитовой купол. На каждой планете можно построить только один малый щитовой купол.';
$lang['info'][408] = 'Дальнейшее развитие малого щитового купола. Он может сдерживать ещё более сильные атаки на планету, поглощая значительно большее количество энергии.';

$lang['info'][502] = 'Ракеты-перехватчики уничтожают атакующие межпланетные ракеты. Одна ракета-перехватчик уничтожает одну межпланетную ракету.';
$lang['info'][503] = 'Межпланетные ракеты уничтожают защиту противника. Уничтоженные межпланетными ракетами оборонительные сооружения больше не восстанавливаются.';

$lang['info'][601] = "Геолог - это признанный эксперт в астроминералогии и кристаллографии. Он со своей командой металлургов и химиков он поддерживает межпланетные правительства при разработке новых источников ресурсов и оптимизации их очистки.<table><tr><td><img src=\"/images/officiers/601.gif\"></td><td><br><font color=\"#84CFEF\">+25% доход от шахт</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">+25% вместимость хранилищ</font><td></tr></table>";
$lang['info'][602] = "Адмирал - это испытанный войной ветеран и гениальный стратег. Даже в самых горячих боях он не теряет обзора и поддерживает контакт с подчинёнными ему адмиралами. Мудрый правитель может полностью положиться на него в бою и тем самым использовать для боя больше кораблей.<table><tr><td><img src=\"/images/officiers/602.gif\"></td><td><br><font color=\"#84CFEF\">Макс. кол-во флотов +2</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">+25% к скорости кораблей</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">-10% затраты на строительство кораблей</font></td></tr></table>";
$lang['info'][603] = "Инженер - это специалист по управлению энергией. В мирное время он повышает уровень энергетических сетей на колониях. В случае нападения он снабжает энергетические системы планетарных защит и предотвращает перегрузки, что ведёт к значительно меньшей степени потерь в бою.<table><tr><td><img src=\"/images/officiers/603.gif\"></td><td><br><font color=\"#84CFEF\">-50% потери при обороне</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">+15% выработка энергии</font><td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">-10% затраты на строительство обороны</font><td></tr></table>";
$lang['info'][604] = "Гильдия технократов - это гениальные учёные, и их всегда можно найти там, где заканчивается грань технически возможного. Их код никогда не сможет разгадать ни один нормальный человек, и одним своим присутствием они вдохновляют учёных империи.<table><tr><td><img src=\"/images/officiers/604.gif\"></td><td><br><font color=\"#84CFEF\">+2 к уровню шпионажа</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">-25% времени на исследования</font><td></tr></table>";
$lang['info'][605] = "Архитектор - это незаменимый специалист в любом строительстве. Благодаря исследованиям и экспериментам, связанным с проектным решением, авторскому надзору за строительством и научному подходу, позволяет ускорить строительство и увеличить КПД строителей.<table><tr><td><img src=\"/images/officiers/605.gif\"></td><td><br><font color=\"#84CFEF\">+2 к очереди построек</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">-25% времени на постройку</font><td></tr></table>";
$lang['info'][606] = "Метафизик - это высококласный специалист в лунном деле. Благодаря своим знаниям он может усиливать гравитационную пушку Звезды Смерти, что позволяет эффективнее применять её в уничтожении лун.<table><tr><td><img src=\"/images/officiers/606.gif\"></td><td><br><font color=\"#84CFEF\">+25% мощности ЗС в уничтожении луны</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">-25% шанса уничтожения ЗС</font><td></tr></table>";
$lang['info'][607] = "Наёмник - высококласный специалист в военном деле. Нанятый к вам на службу, он помогает увеличить эффективность вашего вооружения.<table><tr><td><img src=\"/images/officiers/607.gif\"></td><td><br><font color=\"#84CFEF\">+10% Вооружение</font></td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">+10% Щиты</font><td></tr><tr><td>&nbsp;</td><td><font color=\"#84CFEF\">+10% Броня</font><td></tr></table>";

$lang['info'][701] = '+15% к добыче металла<br>+10% к скорости постройки кораблей<br>+15% к энергии спутников<br>-10% к стоимости улучшения кораблей<br>Уникальный корабль: Корвет';
$lang['info'][702] = '+15% к добыче дейтерия<br>-10% к стоимости постройки кораблей<br>+20% к вместимости хранилищ<br>+5% к энергии от солнечных батарей<br>Уникальный корабль: Перехватчик';
$lang['info'][703] = '+5% к добыче всех ресурсов<br>-5% к стоимости обороны<br>+10% к скорости постройки зданий<br>-5% к стоимости постройки зданий<br>Уникальный корабль: Дредноут';
$lang['info'][704] = '+15% к добыче кристаллов<br>+10% к скорости полёта кораблей<br>+5% энергии от электростанций<br>-10% к стоимости исследований<br>Уникальный корабль: Корсар';

?>