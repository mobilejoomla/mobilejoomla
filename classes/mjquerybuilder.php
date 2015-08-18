<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

class MjQueryBuilder
{
    /** @var JDatabaseDriver|MjDatabaseWrapper */
    protected $dbo;
    /** @var JDatabaseQuery */
    protected $query;
    /** @var bool */
    protected $hasQN;
    /** @var int */
    protected $_limit = 0;

    /**
     * @param $dbo JDatabaseDriver|MjDatabaseWrapper
     */
    public function __construct($dbo)
    {
        $this->dbo = $dbo;
        $this->query = $dbo->getQuery(true);
        $this->hasQN = method_exists($this->query, 'quoteName');
    }

    public function qn($name)
    {
        return $this->hasQN ? $this->query->quoteName($name) : $this->dbo->nameQuote($name);
    }

    public function q($value)
    {
        return $this->dbo->quote($value);
    }

    public function select($columns)
    {
        foreach (func_get_args() as $column) {
            $this->query->select(strpbrk($column, ' (') === false ? $this->qn($column) : $column);
        }
        return $this;
    }

    public function delete($table = null)
    {
        $this->query->delete($table);
        return $this;
    }

    public function insert($tables)
    {
        $this->query->insert($tables);
        return $this;
    }

    public function update($table)
    {
        $this->query->update(strpos($table, ' ') === false ? $this->qn($table) : $table);
        return $this;
    }

    public function from($tables)
    {
        foreach (func_get_args() as $table) {
            $this->query->from(strpos($table, ' ') === false ? $this->qn($table) : $table);
        }
        return $this;
    }

    public function join($type, $conditions)
    {
        $this->query->join($type, $conditions);
        return $this;
    }

    public function innerJoin($conditions)
    {
        return $this->join('INNER', $conditions);
    }

    public function outerJoin($conditions)
    {
        return $this->join('OUTER', $conditions);
    }

    public function leftJoin($conditions)
    {
        return $this->join('LEFT', $conditions);
    }

    public function rightJoin($conditions)
    {
        return $this->join('RIGHT', $conditions);
    }

    public function set($conditions, $glue = ',')
    {
        $this->query->set($conditions, $glue);
        return $this;
    }

    public function where($conditions, $glue = 'AND')
    {
        $this->query->where($conditions, $glue);
        return $this;
    }

    public function group($columns)
    {
        foreach (func_get_args() as $column) {
            $this->query->group(strpos($column, ' ') === false ? $this->qn($column) : $column);
        }
        return $this;
    }

    public function having($conditions, $glue = 'AND')
    {
        $this->query->having($conditions, $glue);
        return $this;
    }

    public function order($columns)
    {
        foreach (func_get_args() as $column) {
            $this->query->order(strpos($column, ' ') === false ? $this->qn($column) : $column);
        }
        return $this;
    }

    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * @return JDatabaseDriver|MjDatabaseWrapper
     */
    public function setQuery()
    {
        $this->dbo->setQuery((string)$this->query, 0, $this->_limit);
        return $this->dbo;
    }

    public function dropTable($tables)
    {
        foreach (func_get_args() as $table) {
            $this->dbo->dropTable($table);
        }
    }

    public function renameTable($old, $new)
    {
        $this->dbo->rename($old, $new);
    }

    public function createTable($tableName, $columns, $indices = array(), $extra = array())
    {
        $config = JFactory::getConfig();
        $driver = $config->get('dbtype', 'mysql');

        switch ($driver) {
            case 'mysql':
            case 'mysqli':
                $query = 'CREATE TABLE ';
                if (isset($extra['if_not_exists']) && $extra['if_not_exists'] === true) {
                    $query .= 'IF NOT EXISTS ';
                }
                $query .= $this->qn($tableName);
                $query .= '(';
                $buffer = array();
                foreach ($columns as $columnName => $columnType) {
                    $q = $this->qn($columnName);
                    switch ($columnType['type']) {
                        case 'int':
                        case 'integer':
                            $q .= ' int';
                            break;
                        case 'bigint':
                            $q .= ' int(11)';
                            unset($columnType['size']);
                            break;
                        case 'serial':
                            $q .= ' int(10) unsigned auto_increment';
                            unset($columnType['size'], $columnType['unsigned'], $columnType['autoincrement']);
                            break;
                        case 'varchar':
                            $q .= ' varchar';
                            break;
                        default:
                            throw new Exception("Unsupported field type " . $columnType['type']);
                    }
                    if (isset($columnType['size']) && is_int($columnType['size'])) {
                        $q .= '(' . $columnType['size'] . ')';
                    }
                    if (isset($columnType['unsigned']) && $columnType['unsigned']) {
                        $q .= ' unsigned';
                    }
                    if (isset($columnType['notnull']) && $columnType['notnull']) {
                        $q .= ' not null';
                    }
                    if (isset($columnType['autoincrement']) && $columnType['autoincrement']) {
                        $q .= ' auto_increment';
                    }
                    if (isset($columnType['default'])) {
                        $q .= ' default ' . $columnType['default'];
                    }
                    $buffer[] = $q;
                }

                foreach ($indices as $indexName => $columnList) {
                    switch ($indexName) {
                        case '@primary':
                            $q = array();
                            foreach ($columnList as $columnName) {
                                $q[] = $this->qn($columnName);
                            }
                            $buffer[] = 'PRIMARY KEY (' . implode(',', $q) . ')';
                            break;
                        default:
                            $q = array();
                            foreach ($columnList as $columnName) {
                                $q[] = $this->qn($columnName);
                            }
                            $buffer[] = 'KEY ' . $this->qn($indexName) . ' (' . implode(',', $q) . ')';
                    }
                }

                $query .= implode(', ', $buffer);
                $query .= ')';
                if (isset($columnType['charset'])) {
                    $query .= ' default charset=utf8';
                }

                $this->dbo->setQuery($query);
                $this->dbo->query();
                break;

            case 'postgresql':
            case 'pgsql':
                $query = 'CREATE TABLE ';
                $query .= $this->qn($tableName);
                $query .= '(';
                $buffer = array();
                foreach ($columns as $columnName => $columnType) {
                    $q = $this->qn($columnName);
                    switch ($columnType['type']) {
                        case 'int':
                        case 'integer':
                            $q .= ' int';
                            break;
                        case 'bigint':
                            $q .= ' int(11)';
                            unset($columnType['size']);
                            break;
                        case 'serial':
                            $q .= ' serial';
                            unset($columnType['size'], $columnType['unsigned'], $columnType['autoincrement']);
                            break;
                        case 'varchar':
                            $q .= ' varchar';
                            break;
                        default:
                            throw new Exception("Unsupported field type " . $columnType['type']);
                    }
                    if (isset($columnType['size']) && is_int($columnType['size'])) {
                        $q .= '(' . $columnType['size'] . ')';
                    }
                    if (isset($columnType['unsigned']) && $columnType['unsigned']) {
                        $q .= ' unsigned';
                    }
                    if (isset($columnType['autoincrement']) && $columnType['autoincrement']) {
                        // @todo Should it be implemented?
                    }
                    if (isset($columnType['default'])) {
                        $q .= ' default ' . $columnType['default'];
                    }
                    if (isset($columnType['notnull']) && $columnType['notnull']) {
                        $q .= ' not null';
                    }
                    $buffer[] = $q;
                }

                foreach ($indices as $indexName => $columnList) {
                    switch ($indexName) {
                        case '@primary':
                            $q = array();
                            foreach ($columnList as $columnName) {
                                $q[] = $this->qn($columnName);
                            }
                            $buffer[] = 'PRIMARY KEY (' . implode(',', $q) . ')';
                            break;
                    }
                }

                $query .= implode(', ', $buffer);
                $query .= ')';
                $this->dbo->setQuery($query);
                $this->dbo->query();

                foreach ($indices as $indexName => $columnList) {
                    switch ($indexName) {
                        case '@primary':
                            break;
                        default:
                            $q = array();
                            foreach ($columnList as $columnName) {
                                $q[] = $this->qn($columnName);
                            }
                            $query = 'CREATE INDEX ' . $this->qn($indexName) . ' ON ' . $this->qn($tableName) . ' (' . implode(',', $q) . ')';
                            $this->dbo->setQuery($query);
                            $this->dbo->query();
                    }
                }
                break;
            default:
                throw new Exception("Unsupported database driver");
        }
    }
}