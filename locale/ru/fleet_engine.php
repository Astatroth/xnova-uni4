<?php

if (!defined('INSIDE'))
	die("attemp hacking");

$lang['Metal']        = "металла";
$lang['Crystal']      = "кристалла";
$lang['Deuterium']    = "дейтерия";

$lang['sys_error'] 					= "Ошибка";
$lang['sys_no_vars'] 				= "Ошибка инициализации переменных, обратитесь к администрации!";
$lang['sys_attacker_lostunits'] 	= "Атакующий потерял %s единиц.";
$lang['sys_defender_lostunits'] 	= "Обороняющийся потерял %s единиц.";
$lang['sys_gcdrunits'] 				= "Теперь на этих пространственных координатах находятся %s %s и %s %s.";
$lang['sys_moonproba'] 				= "Шанс появления луны составляет: %d %% ";
$lang['sys_moonbuilt'] 				= "Благодаря огромной энергии огромные куски металла и кристалла соединяются и образуется луна около координат [%d:%d:%d] !";
$lang['sys_attack_title']    		= "%s. Произошёл бой между следующими флотами::";
$lang['sys_attack_attacker_pos']	= "Атакующий %s [%s:%s:%s]";
$lang['sys_attack_techologies'] 	= "Вооружение: %d %% Щиты: %d %% Броня: %d %% ";
$lang['sys_attack_defender_pos'] 	= "Обороняющийся %s [%s:%s:%s]";
$lang['sys_ship_type'] 				= "Тип";
$lang['sys_ship_count'] 			= "Кол-во";
$lang['sys_ship_weapon'] 			= "Вооружение";
$lang['sys_ship_shield'] 			= "Щиты";
$lang['sys_ship_armour'] 			= "Броня";
$lang['sys_destroyed'] 				= "уничтожен";
$lang['sys_attack_attack_wave'] 	= "Атакующий флот делает: %s выстрела(ов) с общей мощностью %s по обороняющемуся. Щиты обороняющегося поглощают %s выстрелов.";
$lang['sys_attack_defend_wave']		= "Обороняющийся флот делает: %s выстрела(ов) с общей мощностью %s по атакующему. Щиты атакующего поглащают %s выстрелов.";
$lang['sys_attacker_won'] 			= "Атакующий выиграл битву!";
$lang['sys_defender_won'] 			= "Обороняющийся выиграл битву!";
$lang['sys_both_won'] 				= "Бой закончился ничьёй!";
$lang['sys_stealed_ressources'] 	= "Он получает %s металла %s %s кристалла %s и %s дейтерия.";
$lang['sys_rapport_build_time'] 	= "Время генерации страницы %s секунд";
$lang['sys_mess_tower'] 			= "Транспорт";
$lang['sys_mess_attack_report'] 	= "Боевой доклад";
$lang['sys_spy_maretials'] 			= "Шпионский доклад от";
$lang['sys_spy_fleet'] 				= "Флот";
$lang['sys_spy_defenses'] 			= "Оборона";
$lang['sys_mess_qg'] 				= "Командование флотом";
$lang['sys_mess_spy_report'] 		= "Шпионский доклад";
$lang['sys_mess_spy_lostproba'] 	= "Шанс на защиту от шпионажа: %d %% ";
$lang['sys_mess_spy_control'] 		= "Контроль";
$lang['sys_mess_spy_activity'] 		= "Шпионская активность";
$lang['sys_mess_spy_ennemyfleet'] 	= "Чужой флот с планеты";
$lang['sys_mess_spy_seen_at'] 		= "был обнаружен вблизи от планеты";
$lang['sys_mess_spy_destroyed'] 	= "Ваши шпионские зонды были уничтожены!";
$lang['sys_object_arrival'] 		= "Прибытие на планету";
$lang['sys_stay_mess_stay'] 		= "Прибытие флота";
$lang['sys_stay_mess_start'] 		= "Ваш флот достигает планеты ";
$lang['sys_stay_mess_back'] 		= "Ваш флот возвращается назад к планете";
$lang['sys_stay_mess_end'] 			= " и привозит следующие виды ресурсов:";
$lang['sys_stay_mess_bend'] 		= " и привозит следующие виды ресурсов:";
$lang['sys_stay_mess_goods'] 		= "%s : %s, %s : %s, %s : %s";
$lang['sys_colo_mess_from'] 		= "Колонизация";
$lang['sys_colo_mess_report'] 		= "Отчёт о колонизации";

$lang['sys_colo_arrival'] 			= "Флот достигает координат ";
$lang['sys_colo_maxcolo'] 			= ", к сожелению заселение невозможно, вы не можете иметь больше чем ";
$lang['sys_colo_allisok'] 			= ", и поселенец начинает осваивать новую планету.";
$lang['sys_colo_badpos']  			= ", и поселенцы нашли тайный смысл в постройках вашей Империи. Они решили совершить переворот.";
$lang['sys_colo_notfree'] 			= ", и когда посленцы прибыли на планету, то она уже была заселена. Экспедиция потеряна.";
$lang['sys_colo_planet']  			= " колонизированных планет!";

$lang['sys_base_notfree'] 			= ", и когда военные прибыли на указанное место, то тут они увидели заселённую планету.";
$lang['sys_base_badpos']			= ", строительство базы в данном месте невозможно.";
$lang['sys_base_allisok'] 			= ", и военные начинают строительство базы.";
$lang['sys_base_mess_report'] 		= "Отчёт о колонизации";
$lang['sys_base_mess_from'] 		= "Колонизация";
$lang['sys_base_planet']  			= " военных баз!";

$lang['sys_expe_report'] 			= "Отчёт экспедиции";
$lang['sys_recy_report'] 			= "Системная информация";
$lang['sys_mess_transport'] 		= "Транспорт";
$lang['sys_tran_mess_owner'] 		= "Флот достигает планеты <b>%s</b> %s и доставляет %s %s, %s  %s и %s %s.";
$lang['sys_tran_mess_user']  		= "Флот, отправленный с планеты <b>%s</b> %s, прибыл на <b>%s</b> %s и доставил %s %s, %s  %s и %s %s.";
$lang['sys_stay_mess_user']  		= "Флот, отправленный с планеты <b>%s</b> %s, прибыл на <b>%s</b> %s и расположился на орбите планеты.";
$lang['sys_mess_fleetback'] 		= "Возвращение";
$lang['sys_tran_mess_back'] 		= "Флот возвращается на планету <b>%s</b> %s.";
$lang['sys_recy_gotten'] 			= "Переработчик собрал %s %s и %s %s из поля обломков на координатах %s.";

$lang['sys_gain'] 					= "Добыча: ";
$lang['sys_perte_attaquant'] 		= "Атакующий потерял";
$lang['sys_perte_defenseur'] 		= "Обороняющийся потерял";
$lang['sys_debris'] 				= "Обломки: ";

$lang['sys_expe_blackholl_1']          = "Часть вашего флота засосало в чёрную дыру!";
$lang['sys_expe_blackholl_2']          = "Ваш флот полностью засосало в чёрную дыру!";
$lang['sys_expe_found_goods']          = "Ваша экспедиция нашла богую ресурсами планету!<br>На борт загружено %s %s, %s %s и %s %s";
$lang['sys_expe_found_ships']          = "Ваша экспедиция обнаружила несколько кораблей в отличном состоянии!.<br>К флоту присоединились: ";
$lang['sys_expe_back_home']            = "Ваша экспедиция возращается домой.";
$lang['sys_expe_found_ress_1_1']       = 'Ваша экспедиция нашла маленькое скопление астероидов, из которого можно добыть некоторые ресурсы.';
$lang['sys_expe_found_ress_1_2']       = 'На отдалённом астероиде было найдено некоторое количество ресурсов. Ресурсы подняты на борт.';
$lang['sys_expe_found_ress_1_3']       = 'Ваша экспедиция наталкнулась на очень старые обломки космических кораблей давно произошедшей битвы. Можно подобрать отдельные компоненты для повторного использования.';
$lang['sys_expe_found_ress_1_4']       = 'Экспедиция натолкнулась на астероид, заражённый радиоактивными веществами. Тем не менее, сканирование показало, что этот астероид богат сырьём. Использование беспилотных кораблей дало возможность загрузить на борт ресурсы.';
$lang['sys_expe_found_ress_2_1']       = 'Ваша экспедиция нашла древнее, полностью нагруженное грузовое судно, но без экипажа на борту. Ресурсы были перегружены в хранилища Ваших кораблей.';
$lang['sys_expe_found_ress_2_2']       = 'На небольшой луне с собственной атмосферой Ваша экспедиция нашла большое количество сырья. Наземные экипажи собрали эти природные богатства и переправили на борт.';
$lang['sys_expe_found_ress_2_3']       = 'Мы встретили небольшой конвой гражданских кораблей, которые остро нуждались в продовольствии и медикаментах. В обмен на это мы получили огромное количество ресурсов.';
$lang['sys_expe_found_ress_3_1']       = 'Ваша экспедиция сообщает об огромных обломках кораблей чужих. Учёные не смогли разобраться с их технологией, но некоторые составные части кораблей, которые могли быть использованы в качестве сырья, были погружены на борт.';
$lang['sys_expe_found_ress_3_2']       = 'Минеральный пояс вокруг неизвестной планеты содержит большое количество сырья. Экспедиция сообщает о полной загрузке трюмов!';
$lang['sys_expe_found_dm_1_1']         = 'Экспедиции удалось найти и доставитьна борт немного кредитов.';
$lang['sys_expe_found_dm_1_2']         = 'Ваша экспедиция обнаружила корабль-призрак, который перевозил небольшое количество кредитов. Мы не смогли найти доказательство того, что произошло с его экипажем, однако, наши специалисты смогли переправить кредиты с корабля-призрака на борт экспедиции.';
$lang['sys_expe_found_dm_1_3']         = 'Мы наталкнулись на маленький корабль передставителя инопланетной расы, который в обмен на несколько простых математических расчётов предоставил нам небольшой контейнер с кредитами.';
$lang['sys_expe_found_dm_1_4']         = 'Мы нашли остатки корабля чужих. На борту был небольшой контейнер с кредитами!';
$lang['sys_expe_found_dm_1_5']         = 'Экспедиция следовала за какими-то странными сигналами и обнаружила маленьких размеров астероид, в ядре которого находились кредиты. Астероид был доставлен на борт и учёные извлекли из него немного кредитов.';
$lang['sys_expe_found_ships_1_1']      = 'Мы натолкнулись на остатки предыдущей экспедиции! Наши техники пытаются найти среди обломков что-то летающее.';
$lang['sys_expe_found_ships_1_2']      = 'Мы нашли покинутую пиратскую базу. В ангаре находятся несколько старых кораблей. Наши инженеры пытаются найти корабли, которые ещё пригодны для полётов.';
$lang['sys_expe_found_ships_1_3']      = 'Наша экспедиция нашла планету, которая была почти полностью разрушена длительными войнами. На орбите находились различные корабельные обломки. Инженеры пытаются отремонтировать некоторые из них. Вероятно, мы сможем получить информацию о том, что здесь произошло.';
$lang['sys_expe_found_ships_1_4']      = 'Ваша экспедиция обнаружила старинную космическую крепость, которую покинули как будто бы целую вечность назад. В ангаре крепости было найдено несколько кораблей. Специалисты пытаются найти корабли, которые ещё пригодны для полётов. ';
$lang['sys_expe_found_ships_2_1']      = 'Мы нашли остатки армады Специалисты нашли несколько исправных кораблей и пытаются отремонтировать их.';
$lang['sys_expe_found_ships_2_2']      = 'Ваша экспедиция натолкнулась на старую автоматическую верфь. Несколько кораблей всё ещё находятся в стадии производства. Наши специалисты пытаются восстановить энергоснабжение верфи.';
$lang['sys_expe_found_ships_3_1']      = 'Мы нашли огромное кладбаще космических кораблей. Инженерам удалось вернуть к работе несколько кораблей.';
$lang['sys_expe_found_ships_3_2']      = 'Мы обнаружили планету с останками цивилизации. Единственным не разрушенных зданием является космическая станция на орбите планеты. Некоторые из наших инженеров и пилотов отправились туда, чтобы посмотреть, есть ли пригодные для полётов корабли.';
$lang['sys_expe_lost_fleet_1']         = 'от экспедиции осталась только следующая радиограмма: О Боже!... оно... похоже на...';
$lang['sys_expe_lost_fleet_2']         = 'Последним, что было послано экспедицией, были несколько, отлично получившихся, снимков крупным планом раскрывающейся черной дыры.';
$lang['sys_expe_lost_fleet_3']         = 'Поломка ядерного реактора привела к цепной реакции, которая повлекла за собой невероятный взрыв, уничтоживший всю экспедицию.';
$lang['sys_expe_lost_fleet_4']         = 'Экспедиционный флот не возвратился после гиперскачка. Учёные всё ещё гадают, что могло произойти, однако, флот потерян навсегда.';
$lang['sys_expe_time_fast_1']          = 'Непредвиденное обратное сцепление в энергетических приводах ускорило обратный скачок экспедиции, так что она теперь возвратится раньше, чем ожидалось. Согласно первым сообщениям, за время экспедиции не произошло ничего интересного.';
$lang['sys_expe_time_fast_2']          = 'Очень смелый новый командир использовал нестабильный вихревой поток, чтобы уменьшить время обратного полёта – успешно! Однако, сама экспедиция не принесла ничего нового.';
$lang['sys_expe_time_fast_3']          = 'Ваша экспедиция сообщила об отсутствии особенностей в исследованном секторе. Однако, флот при обратном скачке попал под солнечный ветер. Вследствие этого скачок несколько ускорился. Теперь экспедиция вернётся домой несколько раньше.';
$lang['sys_expe_time_slow_1']          = 'Халтурно собранный навигатор неправильно произвёл расчёты для прыжка, и поэтому флот не только приземлился в абсолютно другом месте, но и обратная дорога заняла невероятно много времени.';
$lang['sys_expe_time_slow_2']          = 'По неизвестным причинам скачок экспедиции был произведён неправильно. Экспедиция чуть было не вышла из скачка в центре солнца. К счастью этого не произошло и мы оказались в неизвестной системе. Обратный путь займёт больше времени, чем предполагалось изначально.';
$lang['sys_expe_time_slow_3']          = 'Новый модуль навигации работает, но всё ещё с некоторыми ошибками. Не только скачок был произведён в неправильном направлении, но и израсходовался весь запас топлива. Экспедиции пришлось возвращаться на импульсе. Возвращение займёт достаточно много времени.';
$lang['sys_expe_time_slow_4']          = 'Ваша экспедиция попала в сектор с сильным штормом частиц. Вследствие этого все основные системы вышли из строя. Инженеры смогли предотвратить наихудшее, однако, экспедиция вернётся с некоторым опозданием.';
$lang['sys_expe_time_slow_5']          = 'Флагманский корабль Вашей экспедиции столкнулся с неизвестное судно, которое нарвалось на него без предупреждения. Неизвестное судно взорвалось, нанеся флагману существенные повреждения. В этом состоянии движение невозможно. Как только поверхностный ремонт будет завершён, Ваши корабли начнут возвращаться домой.';
$lang['sys_expe_time_slow_6']          = 'Космический ветер красного гиганта сфальсифицировали скачок экспедиции так, что потребовалось некоторое время для расчета возврата. Кроме того, в секторе, в которой появилась экспедиция, не было ничего, не считая пустоты между звездами.';
$lang['sys_expe_nothing_1']            = 'Экспедиция не принесла ничего особого, кроме какой-то странной зверушки с неизвестной болотной планеты.';
$lang['sys_expe_nothing_2']            = 'Ваша экспедиция сделала красивые снимки сверхновой звезды. Тем не менее, экспедиция не принесла никаких новых сведений. Но есть хорошая возможность победить в конкурсе лучшего снимка вселенной в этом году.';
$lang['sys_expe_nothing_3']            = 'Вскоре, после выхода из Сонечной системы, странный компьютерный вирус парализовал систему навигации. Это привело к тому, что экспедиционные корабли всё время летали по кругу. Излишне говорить, что экспедиция была не особо успешна.';
$lang['sys_expe_nothing_4']            = 'Форма жизни из чистой энергии позаботилась о том, чтобы все члены экспедиционной команды целыми днями пристально смотрели на гипнотические образы на экранах. Когда, наконец, у большинства прояснилось в голове, экспедиция должна была прекратиться из-за острой нехватки дейтерия.';
$lang['sys_expe_nothing_5']            = 'Ну, по крайней мере, теперь мы знаем, что красные аномалии пятого класса могут вызвать не только непредсказуемые воздействия на корабельные системы, но и массовые галлюцинации у экипажа. Однако, больше ничего эта экспедиция не принесла.';
$lang['sys_expe_nothing_6']            = 'Не смотря на первые, многообещающие сканирования этого сектора экспедиция, к сожалению, возвращается с пустыми руками.';
$lang['sys_expe_nothing_7']            = 'Вероятно, не нужно было праздновать день рождения капитана на этой отдаленной планете. Противная желтая лихорадка заставила большинство экипажа провести всю экспедицию в лазарете. Острая нехватка персонала привела к тому, что экспедиция потерпела неудачу.';
$lang['sys_expe_nothing_8']            = 'Ваша экспедиция, в буквальном смысле, познакомился с пустотой вселенной. Не было ни маленьких астероидов, ни излучения, ни частиц, ничего, что заинтересовало бы экспедицию.';
$lang['sys_expe_nothing_9']            = 'Ошибка руководства, ответственного за реактор корабля чуть было не уничтожила всю экспедицию. К счастью, инженеры смогли предотвратить наихудшее. Тем не менее, ремонт реактора занял очень много времени, поэтому экспедиция вернулась с пустыми руками.';
$lang['sys_expe_attack_1_1_1']         = 'Пара отчаянных космических пиратов попыталась захватить наш флот.';
$lang['sys_expe_attack_1_1_2']         = 'Несколько примитивных варваров атаковали нас космическими кораблями, которые даже космическими кораблями назвать нельзя. Если обстрел примет серьёзный оборот, мы вынуждены будем ответить на огонь.';
$lang['sys_expe_attack_1_1_3']         = 'Мы перехватили несколько радиограмм очень пьяных пиратов. По-видимому, на нас должны напасть.';
$lang['sys_expe_attack_1_1_4']         = 'Мы должны были защищаться от нескольких пиратов, которых, к счастью, было не слишком много.';
$lang['sys_expe_attack_1_1_5']         = 'Ваши экспедиционные корабли сообщают, что некий Тикар Моа и его дикая свора требуют безусловный капитуляции Ваших флотов. Если они не шутят, они должны понимать, что все корабли прекрасно могут защищаться.';
$lang['sys_expe_attack_1_2_1']         = 'У Вашей экспедиции произошла неприятная встреча с космическими пиратами.';
$lang['sys_expe_attack_1_2_2']         = 'Мы попали в засаду нескольких космических пиратов! Бой, к сожалению, был неизбежен.';
$lang['sys_expe_attack_1_2_3']         = 'Крик о помощи, за которым следовала экспедиция, оказался ловушкой нескольких озлобленных космических пиратов. Бой был неизбежен.';
$lang['sys_expe_attack_1_3_1']         = 'Пойманные сигналы исходили не от инопланетной расы, а от секретной пиратской базы! Они были не в особом восторге от нашего появления.';
$lang['sys_expe_attack_1_3_2']         = 'Экспедиция сообщает о тяжёлых боях с неопознанными пиратскими кораблями!';
$lang['sys_expe_attack_2_1_1']         = 'Ваш экспедиционный флот испытал не особо дружественный первый контакт с неизвестной расой.';
$lang['sys_expe_attack_2_1_2']         = 'Несколько необычных с виду кораблей атаковали экспедицию без предупреждения!';
$lang['sys_expe_attack_2_1_3']         = 'Ваша экспедиция была атакована небольшой группой неизвестных кораблей!';
$lang['sys_expe_attack_2_1_4']         = 'Экспедиция сообщила о контакте с неизвестными кораблями. Радиограммы не расшифровываются, однако, чужие корабли, кажется, активируют своё оружие.';
$lang['sys_expe_attack_2_2_1']         = 'Неизвестные существа напали на Вашу экспедицию!';
$lang['sys_expe_attack_2_2_2']         = 'Ваша экспедиция пересекла космическую границу до сих пор неизвестной, но весьма агрессивной и воинственной инопланетной расы.';
$lang['sys_expe_attack_2_2_3']         = 'В скором времени связь с экспедицией была нарушена. Если мы правильно расшифровали последнее сообщение, флот находился под шквальным огнём - агрессоры не были идентифицированы.';
$lang['sys_expe_attack_2_3_1']         = 'Ваша экспедиция подверглась атаке чужих. Развязался ожесточённый бой!';
$lang['sys_expe_attack_2_3_2']         = 'Большое объединение кристаллических судов держит курс на Вашу экспедицию. Мы должны предполагать наихудшее.';
$lang['sys_expe_attackname_1']         = 'Пираты';
$lang['sys_expe_attackname_2']         = 'Инопланетяне';
$lang['sys_expe_back_home']            = 'Ваш флот вернулся из экспедиции.<br>Флот доставил груз: %s %s, %s %s, %s %s.';
$lang['sys_expe_back_home_without_dm'] = 'Ваш флот вернулся из экспедиции ни с чем.';
$lang['sys_expe_back_home_with_dm']    = 'Ваш флот вернулся из экспедиции.<br>Найденно %s(%s) повреждённых кораблей.<br>Но %s ещё можно отремонтировать.';

$lang['sys_moon_destruction_report'] 	= "Рапорт разрушения луны";
$lang['sys_moon_destroyed']          	= "Ваши Звёзды Смерти произвели мощную гравитационную волну, которая разрушила луну! ";
$lang['sys_rips_destroyed']          	= "Ваши Звёзды Смерти произвели мощную гравитационную волну, но её мощности оказалось не достаточно для уничтожения луны такого размера. Но гравитационная волна отразилась от лунной поверхности и разрушила ваш флот.";
$lang['sys_rips_come_back']          	= "Ваши Звёзды Смерти не имеют достаточно энергии, чтобы нанести ущерб этой луне. Ваш флот возвращается не уничтожив луну.";
$lang['sys_chance_moon_destroy']     	= "Шанс уничтожения луны: ";
$lang['sys_chance_rips_destroy']     	= "Шанс уничтожения ЗС: ";

$lang['sys_mess_destruc_report']       = 'Доклад сноса луны';
$lang['sys_destruc_lune']              = 'Вероятность уничтожения луны составила: ';
$lang['sys_destruc_rip']               = 'Вероятность взрыва звёзд смерти составила: ';
$lang['sys_destruc_stop']              = 'Обороняющийся был в состоянии остановить уничтожение луны.';
$lang['sys_destruc_mess1']             = 'Звезда смерти выпустила гравитон на орбиту луны. ';
$lang['sys_destruc_echec']             = 'Что-то идёт не так, как надо, гравитон в реакторах вызывает реакцию и звёзды смерти разлетаются на куски.';
$lang['sys_destruc_all']               = 'Всё более усиливающиеся толчки сотрясают спутник. Луна начинает деформироваться и разрывается в конце концов на миллионы кусочков. Внезапно ваш флот исчезает с экранов радаров. Что-то там у них не так, наверное пришибло обломками...';
$lang['sys_destruc_reussi']            = 'На поверхности луны появляются гигантские трещины, вскоре луна раскалывается на куски, миссия выполнена. Флот возвращается домой.';
$lang['sys_destruc_null']              = 'Звёзды смерти не произвели достаточно мощный выстрел, миссия не выполнена. Флот возвращается домой.';

$lang['sys_stat_mess']               = 'Ваш флот достиг планеты %s и доставил груз: %s %s, %s %s и %s %s.';

?>