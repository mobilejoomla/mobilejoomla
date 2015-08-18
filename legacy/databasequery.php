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

class MjDatabaseQueryElement
{
    /** @var string */
    protected $_name;
    /** @var array */
    protected $_elements = array();
    /** @var string */
    protected $_glue;

    public function __construct($name, $elements, $glue = ',')
    {
        $this->_name = $name;
        $this->_glue = $glue;
        $this->append($elements);
    }

    public function __toString()
    {
        return PHP_EOL . $this->_name . ' ' . implode($this->_glue, $this->_elements);
    }

    public function append($elements)
    {
        $this->_elements = array_unique(array_merge($this->_elements, (array)$elements));
    }
}

class MjDatabaseQuery
{
    protected $_type = '';
    /** @var MjDatabaseQueryElement */
    protected $_select;
    /** @var MjDatabaseQueryElement */
    protected $_delete;
    /** @var MjDatabaseQueryElement */
    protected $_update;
    /** @var MjDatabaseQueryElement */
    protected $_insert;
    /** @var MjDatabaseQueryElement */
    protected $_from;
    /** @var MjDatabaseQueryElement[] */
    protected $_join;
    /** @var MjDatabaseQueryElement */
    protected $_set;
    /** @var MjDatabaseQueryElement */
    protected $_where;
    /** @var MjDatabaseQueryElement */
    protected $_group;
    /** @var MjDatabaseQueryElement */
    protected $_having;
    /** @var MjDatabaseQueryElement */
    protected $_order;

    public function clear($clause = null)
    {
        switch ($clause) {
            case 'select':
                $this->_select = null;
                $this->_type = null;
                break;
            case 'delete':
                $this->_delete = null;
                $this->_type = null;
                break;
            case 'update':
                $this->_update = null;
                $this->_type = null;
                break;
            case 'insert':
                $this->_insert = null;
                $this->_type = null;
                break;
            case 'from':
                $this->_from = null;
                break;
            case 'join':
                $this->_join = null;
                break;
            case 'set':
                $this->_set = null;
                break;
            case 'where':
                $this->_where = null;
                break;
            case 'group':
                $this->_group = null;
                break;
            case 'having':
                $this->_having = null;
                break;
            case 'order':
                $this->_order = null;
                break;
            default:
                $this->_type = null;
                $this->_select = null;
                $this->_delete = null;
                $this->_update = null;
                $this->_insert = null;
                $this->_from = null;
                $this->_join = null;
                $this->_set = null;
                $this->_where = null;
                $this->_group = null;
                $this->_having = null;
                $this->_order = null;
                break;
        }
        return $this;
    }

    public function select($columns)
    {
        $this->_type = 'select';
        if ($this->_select === null) {
            $this->_select = new MjDatabaseQueryElement('SELECT', $columns);
        } else {
            $this->_select->append($columns);
        }
        return $this;
    }

    public function delete($table = null)
    {
        $this->_type = 'delete';
        $this->_delete = new MjDatabaseQueryElement('DELETE', null);
        if (!empty($table)) {
            $this->from($table);
        }
        return $this;
    }

    public function insert($tables)
    {
        $this->_type = 'insert';
        $this->_insert = new MjDatabaseQueryElement('INSERT INTO', $tables);
        return $this;
    }

    public function update($tables)
    {
        $this->_type = 'update';
        $this->_update = new MjDatabaseQueryElement('UPDATE', $tables);
        return $this;
    }

    public function from($tables)
    {
        if ($this->_from === null) {
            $this->_from = new MjDatabaseQueryElement('FROM', $tables);
        } else {
            $this->_from->append($tables);
        }
        return $this;
    }

    public function join($type, $conditions)
    {
        if ($this->_join === null) {
            $this->_join = array();
        }
        $this->_join[] = new MjDatabaseQueryElement(strtoupper($type) . ' JOIN', $conditions);
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
        if ($this->_set === null) {
            $glue = strtoupper($glue);
            $this->_set = new MjDatabaseQueryElement('SET', $conditions, "\n\t$glue ");
        } else {
            $this->_set->append($conditions);
        }
        return $this;
    }

    public function where($conditions, $glue = 'AND')
    {
        if ($this->_where === null) {
            $glue = strtoupper($glue);
            $this->_where = new MjDatabaseQueryElement('WHERE', $conditions, " $glue ");
        } else {
            $this->_where->append($conditions);
        }
        return $this;
    }

    public function group($columns)
    {
        if ($this->_group === null) {
            $this->_group = new MjDatabaseQueryElement('GROUP BY', $columns);
        } else {
            $this->_group->append($columns);
        }
        return $this;
    }

    public function having($conditions, $glue = 'AND')
    {
        if ($this->_having === null) {
            $glue = strtoupper($glue);
            $this->_having = new MjDatabaseQueryElement('HAVING', $conditions, " $glue ");
        } else {
            $this->_having->append($conditions);
        }
        return $this;
    }

    public function order($columns)
    {
        if ($this->_order === null) {
            $this->_order = new MjDatabaseQueryElement('ORDER BY', $columns);
        } else {
            $this->_order->append($columns);
        }
        return $this;
    }

    public function __toString()
    {
        $query = '';
        switch ($this->_type) {
            case 'select':
                $query .= (string)$this->_select;
                $query .= (string)$this->_from;
                if ($this->_join) {
                    foreach ($this->_join as $join) {
                        $query .= (string)$join;
                    }
                }
                if ($this->_where) {
                    $query .= (string)$this->_where;
                }
                if ($this->_group) {
                    $query .= (string)$this->_group;
                }
                if ($this->_having) {
                    $query .= (string)$this->_having;
                }
                if ($this->_order) {
                    $query .= (string)$this->_order;
                }
                break;
            case 'delete':
                $query .= (string)$this->_delete;
                $query .= (string)$this->_from;
                if ($this->_join) {
                    foreach ($this->_join as $join) {
                        $query .= (string)$join;
                    }
                }
                if ($this->_where) {
                    $query .= (string)$this->_where;
                }
                break;
            case 'update':
                $query .= (string)$this->_update;
                $query .= (string)$this->_set;
                if ($this->_where) {
                    $query .= (string)$this->_where;
                }
                break;
            case 'insert':
                $query .= (string)$this->_insert;
                $query .= (string)$this->_set;
                if ($this->_where) {
                    $query .= (string)$this->_where;
                }
                break;
        }
        return $query;
    }

    public function qn($name)
    {
        $db = JFactory::getDbo();
        return $db->nameQuote($name);
    }
}