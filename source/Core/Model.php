<?php

namespace Source\Core;

use Exception;
use Generator;
use PDOException;
use Source\Core\Connect;
use Source\Validate;
use stdClass;

/**
 * Class abstrct Model
 * @description 
 * @package Source\Model
 */
abstract class Model
{
    /** @var stdClass $datas */
    protected $datas;
    
    /** @var string $select */
    protected $select;

    /** @var string $entity */
    protected $entity;

    /** @var array $protected */
    protected $protected;

    /** @var array $required */
    protected $required;

    /** @var \PDOException $fail */
    protected $fail;

    /** @var array $params */
    protected $params;

    /** @var string $columns */
    protected $columns = "*";

    /** @var int $limit */
    protected $limit;

    /** @var string $order */
    protected $order;

    /** @var int $offset */
    protected $offset;

    /** @var string $where */
    protected $where;
    
    /** @var string $orWhere */
    protected $orWhere;

    /** @var string $andWhere */
    protected $andWhere;

    /** @var string $join */
    protected $join;

    /** @var string $asJoin */
    protected $asJoin;

    /** @var string $tableJoin */
    protected $tableJoin;

    /** @var string $on */
    protected $on;

    /** @var string $query */
    protected $query ;

    /** @var string $all */
    protected $all ;

    /**
     * Model construct
     * @param string $entity
     * @param array $protected
     * @param array $required
     */
    public function __construct(string $entity, array $protected, array $required)
    {
        $this->entity = $entity;
        $this->protected = $protected;
        $this->required = $required;
    
    }

    /**
     * __set
     */
    public function __set($name, $value)
    {
        if(empty($this->datas)){
            $this->datas = new stdClass();
        }
        $this->datas->$name = $value;
    }

    /**
     * __get
     * @param string $name
     */
    public function __get($name)
    {
        return $this->datas->$name;
    }

    /**
     * __isset
     */
    public function __isset($name)
    {
        return isset($this->datas->$name);
    }

    /**
     * @return null|PDOException
     */
    public function fail(): ?PDOException
    {
        return $this->fail;
    }

    /**
     * datas
     * @Description : Método responsável por retornar dados do Modelo
     * @return object StdClass
     */
    public function datas(): ?stdClass
    {
        return $this->datas;
    }

    /**
     * query
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * where
     * @param string $columns
     * @param string $operator
     * @param string $value
     */
    public function where(string $column, string $operator, string $value)
    {
        $this->where = " WHERE {$column} {$operator} '{$value}'";
        return $this;
    }

    /**
     * orWhere
     * @param string $columns
     * @param string $operator
     * @param string $value
     */
    public function orWhere(string $column, string $operator,  $value)
    {
        $this->orWhere .= " OR {$column} {$operator} '{$value}'"; 
        return $this;
    }

     /**
     * andWhere
     * @param string $columns
     * @param string $operator
     * @param string $value
     */
    public function andWhere(string $column, string $operator, string $value)
    {
        $this->andWhere .= " AND {$column} {$operator} '{$value}'";
        return $this;
    }

    /**
     * get
     * @return object Model
     */
    public function get(): ?Model
    {
        $this->select = "SELECT {$this->columns} FROM {$this->entity}";
        $this->all = false;
        return $this;
    }

    /**
     * getAll
     * @return object Model
     */
    public function getAll(): ?Model
    {
        $this->select = "SELECT {$this->columns} FROM {$this->entity}";
        $this->all = true;
        return $this;
    }

    /**
     * columns
     * @param string $columns
     * @return Model
     */
    public function columns(string $columns): Model
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * all
     * @param string $columns
     */
    public function all()
    {
        $this->select = "SELECT {$this->columns} FROM `{$this->entity}`";
        $this->all = true;
        return $this->fetch();
    }


    /**
     * limit
     * @param int $limit
     * @return Model;
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * orderBy
     * @param string $column
     * @param string $order
     * @return Model
     */
    public function orderBy(string $column, string $order = "ASC"): Model
    {
        $this->order = " ORDER BY {$column} {$order}";
        return $this;
    }

    /**
     * offset
     * @param int $offset
     * @return Model
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /** 
     * joins 
     * @param string $table
     * @return Model
     */
    public function join(string $table): Model
    {
       $this->join .= " INNER JOIN {$table}";
       $this->asJoin = $table;
       $this->tableJoin = $table;
       return $this;
    }

    /**
     * on
     * @param string $onColumnTable
     * @param string $operator
     * @param string $onColumnTabelSecondary
     * @return object $this
     */
    public function on(string $onColumnTable, string $operator, string $onColumnTabelSecondary)
    {
        $this->on .= " ON {$onColumnTable} {$operator} {$onColumnTabelSecondary}";
        return $this;
    }

    /**
     * fetch
     * $param bool $all
     * @param array|Model
     */
    public function fetch()
    {
        try{
            $this->query = $this->select.$this->join.$this->on.$this->where.$this->orWhere.$this->andWhere.$this->order.$this->limit.$this->offset;

            $stmt = Connect::getInstance()->prepare($this->query);
            $stmt->execute($this->params);

            if(!$stmt->rowCount()){
                return null;
            }

            if(!$this->all){
                return $stmt->fetchObject(static::class);
            }
            
            return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
        

        }catch(PDOException $exception)
        {
            $this->fail = $exception;
        }
    }

    /**
     * GeneratorFetch
     * @return null|Generator
     */
    public function generatorFetch(): ?Generator
    {
        try{
            $this->query = $this->select.$this->join.$this->on.$this->where.$this->orWhere.$this->andWhere.$this->order.$this->limit.$this->offset;

            $stmt = Connect::getInstance()->prepare($this->query);
            $stmt->execute($this->params);

            if(!$stmt->rowCount()){
                return null;
            }

            if(!$this->all){
                yield($stmt->fetchObject(static::class));
            }
            
            yield($stmt->fetchAll(\PDO::FETCH_CLASS, static::class));
            
        }catch(PDOException $exception)
        {
            $this->fail = $exception;
            return null;
        }
    }


    /**
     * create
     * array $data
     */
    public function create(array $data)
    {
        try{
            $data = $this->safe($data);
            $columns = implode(", ", array_keys($data));
            $values = ":". implode(", :", array_keys($data));
            $stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->safe($data));

            return Connect::getInstance()->lastInsertId();

        }catch(PDOException $exception){
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * update
     * @param array $data
     * @param string $where
     */
    public function update(string $column, int $value)
    {
        try{
            $dataSet = [];
            $data = $this->safe((array)$this->datas);

            foreach($data as $bind => $valueData){
                $dataSet[] = "{$bind} = :{$bind}";
            }
            $dataSet = implode(", ", $dataSet);      
            
            parse_str(":{$column}={$value}", $params);

            $stmt = Connect::getInstance()->prepare("UPDATE `{$this->entity}` SET {$dataSet} WHERE {$column}=:{$column}");

            $stmt->execute(array_merge($data, $params));

            return ($stmt->rowCount() ?? 1);

        }catch(PDOException $exception){
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * delete
     * @param string $column
     */
    public function delete(string $column, $value)
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM `{$this->entity}` WHERE {$column} = :{$column}");
            parse_str(":{$column}={$value}", $params);
            $stmt->execute($params);
            return true;
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * destroy
     */
    public function destroy()
    {
        if(empty($this->id)){
            return false;
        }
        $destroy = $this->delete("id", $this->id);
        return $destroy;
    }

    /**
     * safe
     * @param array $data
     */
    public function safe(array $data)
    {
        $safe = (array)$data;
        foreach($this->protected as $unset){
            unset($safe[$unset]);
        }
        return $safe;
    }




}