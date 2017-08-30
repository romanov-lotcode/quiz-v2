<?php

/**
 * Class User_Group
 * Группы пользователей
 */
class User_Group
{
    /*****************************************************
     ******************** ПОЛЯ КЛАССА ********************
     *****************************************************/

    const SHOW_BY_DEFAULT = 20;

    /*****************************************************
     ******************* МЕТОДЫ КЛАССА *******************
     *****************************************************/

    /**
     * Получить данные по параметрам поиска
     * @param [] $search - параметры поиска
     * @param int $page - номер страницы
     * @return array
     */
    public static function getUserGroups($search = null, $page = 1)
    {
        $page = intval($page);
        if ($page < 1) $page = 1;

        $offset = ($page - 1) * self::SHOW_BY_DEFAULT;

        $sql = 'SELECT
            *
          FROM
            user_group
          WHERE
            user_group.name LIKE ? AND
            user_group.flag >= 0 AND
            user_group.id > 0
          ORDER BY
            user_group.name LIMIT '. self::SHOW_BY_DEFAULT .' OFFSET ' . $offset;

        $db = Database::getConnection();
        $result = $db->prepare($sql);
        $search['name'] = '%' . $search['name'] . '%';

        $result->execute([$search['name']]);

        // Получение и возврат результатов
        $user_groups = [];
        $i = 0;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $user_groups[$i] = $row;
            $i++;
        }
        return $user_groups;
    }

    /**
     * Возвращет количество записей, удовлетворяющих параметрам поиска
     * @param [] $search - параметры поиска
     * @return int
     */
    public static function getTotalUserGroups($search = null)
    {
        $search['name'] = '%' . $search['name'] . '%';
        $sql = 'SELECT
            COUNT(*) AS row_count
          FROM
            user_group
          WHERE
            user_group.name LIKE ? AND
            user_group.flag >= 0 AND
            user_group.id > 0';

        $db = Database::getConnection();
        $result = $db->prepare($sql);
        $result->execute([$search['name']]);
        // Обращаемся к записи
        $count = $result->fetch(PDO::FETCH_ASSOC);

        if ($count) {
            return $count['row_count'];
        }
        return 0;
    }

    /**
     * Возвращает порядковый номер по номеру страницы
     * @param int $page - номер страницы
     * @return int
     */
    public static function getIndexNumber($page)
    {
        $page = intval($page);
        $result = ($page - 1) * self::SHOW_BY_DEFAULT;
        return $result;
    }

    /**
     * Добавляет новую группу пользователей
     * @param [] $user_group - массив с данными
     * @return bool|int
     */
    public static function add($user_group)
    {
        $sql = 'INSERT INTO user_group (name, change_user_id, change_datetime, flag)
          VALUES (:name, :change_user_id, :change_datetime, :flag)';
        $db = Database::getConnection();
        $result = $db->prepare($sql);
        $result->bindParam(':name', $user_group['name'], PDO::PARAM_STR);
        $result->bindParam(':change_user_id', $user_group['change_user_id'], PDO::PARAM_INT);
        $result->bindParam(':change_datetime', $user_group['change_datetime'], PDO::PARAM_STR);
        $result->bindParam(':flag', $user_group['flag'], PDO::PARAM_INT);
        if($result->execute())
        {
            return $db->lastInsertId();
        }
        return false;
    }
}