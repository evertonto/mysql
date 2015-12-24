<?php

namespace EpClasses\DataBase;

use EpClasses\Helpers\Randomico;
use EpClasses\Helpers\Filters;

/**
 * <b>MySqlConection: </b> Esta Classe realiza os comandos em banco de dados MySql
 * @author tom
 */
class MySqlConection extends Conection
{
    /** @var String Query montada para submissao a base de dados */
    private $query = null;
    
    /** @var Int Último Id inserido no banco de dados*/
    private $lastInsertId = null;
    
    /** @var Objeto \PDOStatement criado para execução de comandos na base de dados */
    private $stmt = null;
    
    /** @var Objeto \PDOConection criado para exercer a ponte de conexao com o bando de dados */
    private $dbInstance = null;
    
    /** @var Array  Armazena os valores que seram feitos bindValues */
    private $toPrepare = array();
    
    /** @var String contém a string sql final */
    private $select = null;
    
    /** @var Array contém as tables, entidades from da query */
    private $from = array();
    
    /** @var Array contém a string sql com todos os joins da aplicação */
    private $joins = array();
    
    /** @var Array contém os valores de filtros where*/
    private $where = array();
    
    /** @var Array contém a configurações para order */
    private $order = array();
    
    /** @var Array contém a configurações para group */
    private $group = array();
    
    /** @var String contém a string limit */
    private $limit = null;
    
    /** @var String contém a procedure a ser executada*/
    private $procedure = null;
    
    
    /**
     * Método construtor para conexao com banco de dados
     * @param \PDO $conection Conexao estabelcida com PDO com o bando de dados
     */
    protected function __construct(\PDO $conection)
    {
        if($this->dbInstance === null):
            $this->dbInstance = $conection;
            $this->dbInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->dbInstance->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
        endif;
    }
    
    /**
     * Select de dados em bando de dados MySql
     * @param Array $args Lista de de table(entidades) e campos que deveram retornar da consulta
     */
    public function select(array $args)
    {
        try
        {
            if(is_array($args) && !empty($args)):
                
                $this->select = ($this->select === null) ? "SELECT" : $this->select. ",";
                $countTable = 1;
                foreach ($args as $table => $fields):
                    
                    $nickname = $this->getFrom($table);
                    if(empty($nickname)):
                        $this->setFrom($table);
                        $nickname = $this->getFrom($table);
                    endif;
                    
                    $countFields = 1;
                    foreach ($fields as $field  => $alias):
                        
                        $vrgl = (count($fields) === $countFields && count($args) === $countTable ) ? "" : ",";
                        if(is_numeric($field)):
                            
                            $this->select .= " {$nickname[0]['nickname']}.{$alias}{$vrgl}";
                        else:
                            
                            $this->select .= " {$nickname[0]['nickname']}.{$field} AS '{$alias}'{$vrgl}";
                        endif; 
                    $countFields++;  
                    endforeach;
                    $countTable++;
                endforeach;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL(SELECT): ".$ex->getMessage();
        }
    }
    
    /**
     * Select de dados em bando de dados MySql
     * @param Array $args Lista de de table(entidades) e campos que deveram retornar da consulta
     */
    public function functions(array $args)
    {
        try
        {
            if(is_array($args) && !empty($args)):
                
                $this->select = ($this->select === null) ? "SELECT" : $this->select . ",";
               
                $countFunctions = 1;
                $countAlias = 0;
                foreach ($args as $function => $tables):

                    if(!is_numeric($function)):
                        
                        $vrgl = ($this->select[strlen($this->select)-1] == ")") ? "," : "";
                        $this->select .= "{$vrgl} {$function}(";
                        $countTable = 1;
                        foreach ($tables as $table => $fields):

                            $countFields = 1;
                            if(!is_numeric($table)):

                                $nickname = $this->getFrom($table);
                                if(empty($nickname)):
                                    $this->setFrom($table);
                                    $nickname = $this->getFrom($table);
                                endif;
                                foreach ($fields as $field):

                                    $vrgl = (count($fields) === $countFields && count($tables) === $countTable ) ? "" : ",";
                                    $this->select .= "{$nickname[0]['nickname']}.{$field}{$vrgl}";
                                    $countFields++;  
                                endforeach;
                            else:
                                foreach ($fields as $field):

                                    $vrgl = (count($fields) === $countFields && count($tables) === $countTable ) ? "" : ",";
                                    $this->select .= "{$field}{$vrgl}";
                                    $countFields++;  
                                endforeach;
                            endif;
                        $countTable++;
                        endforeach;
                        $this->select .= ")";
                    else:
                        
                        $vrgl = (count($args) === $countFunctions) ? "" : ",";
                        if(!empty($args[$countAlias])):
                            $this->select .= " AS '{$args[$countAlias]}'{$vrgl}";
                        else:
                            $this->select .= "{$vrgl}";
                        endif;
                        $countAlias++;
                    endif;
                    $countFunctions++;
                endforeach;
            endif;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL(SELECT P/ FUNCTIONS): ".$ex->getMessage();
        }
    }
    
    /**
     * Condição Join em bando de dados MySql
     * @param array $args Lista de campos a serem feito join
    */
    public function join(array $args)
    {
        
    }
    
    /**
     * Condição leftJoin em bando de dados MySql
     * @param array $args Lista de campos a serem feito leftJoin
    */
    public function leftJoin(array $args)
    {
        
    }
    
    /**
     * Condição rightJoin em bando de dados MySql
     * @param array $args Lista de campos a serem feitos rightJoin
    */
    public function rightJoin(array $args)
    {
        
    }
    
    /**
     * Condição where em bando de dados MySql
     * @param array $args Lista de condições WHERE da consulta
    */
    public function where(array $args)
    {
        
    }
    
    /**
     * Condição order em bando de dados MySql
     * @param array $args Lista de condições ORDER da consulta
    */
    public function order(array $args)
    {
        
    }
    
    /**
     * Condição group em bando de dados MySql
     * @param array $args Lista de condições GROUP da consulta
    */
    public function group(array $args)
    {
        
    }
    
    /**
     * Condição limit em bando de dados MySql
     * @param Int $args int com LIMIT de retorno da consulta
    */
    public function limit($args)
    {
        try
        {
            $this->limit = (int)$args;
        }
        catch (\Exception $ex)
        {
            echo "ERRO DE CONSTRUÇÃO DE SQL (LIMIT): ".$ex->getMessage();
        }
    }
    
    /**
     * Insert de dados em bando de dados MySql
     * @param type $table Tabela, View a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem inseridos
     * @return boolean true|false
     */
    public function insert($table, array $args)
    {
        
    }
    
    /**
     * Delete de dados em bando de dados MySql
     * @param type $table Tabela a ser feito delete no bando de dados
     * @return boolean true|false
     */
    public function delete($table)
    {
        
    }
    
    /**
     * Update de dados em bando de dados MySql
     * @param type $table Tabela, View a ser feito insert no bando de dados
     * @param array $args Lista de campos e valores a serem feitos atualização
     * @return boolean true|false
     */
    public function update($table, array $args = null)
    {
        
    }
    
    /**
     * Chama procedures no banco de dados
     * @param Array $arg argumentos com nome da procedure
     */
    public function procedure(array $arg)
    {
        
    }
    
    /**
     * Serialização dos valores recebido de consulta.
     * @param Object $type Tipo de fetch a ser realizado
     *                    PDO::FETCH_ASSOC
     *                    PDO::FETCH_BOTH
     *                    PDO::FETCH_CLASS
     *                    e outros
     * @param String $class Indica a qual object deseja-se transforma o retorno da consulta
     * @return Object|Array
     */
    public function fetch($type = null , $class = null)
    {
        $this->query = $this->constructQuery();
        
        if($this->query !== null):
            
            $callback = null;
            $this->stmt = $this->dbInstance->prepare($this->query);

            if($this->stmt->execute()):

                if($type === null && $class === null):

                    $callback = $this->stmt->fetchAll();
                elseif($class !== null && $type !== null):

                    $callback = $this->stmt->fetchAll($type, $class);
                elseif(is_string($type) && $class === null):

                    $callback = $this->stmt->fetchAll(\PDO::FETCH_CLASS, $type);
                else:

                    $callback = $this->stmt->fetchAll($type);
                endif;
            endif;

            $this->clear();
            
            return $callback;
        endif;
        return array('AVISO'=>'É preciso estabelecer o método de pesquisa novamente, após a execução do fetch() ou execute(), a consulta é deletada');
    }
    
    /**
     * Procedimento executa as operações no bando de dados
     * @return boolean true|false
     * @throws \Exception Erro na tentiva de execução junto ao bando de dados
     */
    public function execute()
    {
        try
        {
            if($this->stmt instanceof \PDOStatement):
                
                return $this->stmt->execute();
            endif;
        }
        catch (\PDOException $e)
        {
            throw new \Exception("ERRO SMTP: ".$e->getMessage());
        }
    }
       
    /**
     * Retorna a Query formada a ser submetida a base de dados
     * @return String 
     */
    public function getStringQuery()
    {
        return $this->constructQuery();
    }
    
    
    /**
     * Retorna o último Id inserido
     * @return Int
     */
    public function getLastInsertId()
    {
        return (int) $this->lastInsertId;
    }
        
    /**
     * Limpar as propriedades da Classe
     */
    private function clear()
    {
        $this->select = null;
        
        $this->from = array();
        unset($this->from);
        
        unset($this->joins);
        $this->joins = array();
        
        unset($this->where);
        $this->where = array();
        
        unset($this->order);
        $this->order = array();
        
        unset($this->group);
        $this->group = array();    
        
        $this->limit = null;
        
        $this->procedure = null;
        
        $this->stmt = null;
        $this->query = null;
        
        unset($this->toPrepare);
        $this->toPrepare = array();
    }
    
    /**
     * Esta função constroe tada a query para execução no banco de dados
     * @return String Query construida para perpetuar select
     */
    private function constructQuery()
    {
        $query = null;
        if($this->select !== null):
            $query = $this->select." FROM";
            
            $count = 1;
            foreach ($this->from as $from):
                $vrgl = (count($this->from) === $count) ? "" : "," ;
                $query .= " {$from['table']} {$from['nickname']}{$vrgl}";
                $count++;
            endforeach;
        
            
            if(!empty($this->joins)):
                
            endif;
            
            if(!empty($this->where)):
                
            endif;
            
            if(!empty($this->order)):
                
            endif;
            
            if(!empty($this->group)):
                
            endif;
            
            if($this->limit !== null):
                $query .= " LIMIT ".$this->limit;
            endif;
            
        endif;
            
        return $query;
    }
    
    /**
     * submitNickname (apelidos) a serem usados nas entidades objetos de pesquisa
     * @param String $table Nome da tabela (entidade)
     * @return String
     */
    private function setFrom($table)
    {
        $find = false;
        while(!$find):
            
            $randomLetters = Randomico\Random::getRandomLetters(3, Randomico\Random::LOWERCASE);
            if(empty($this->getFrom($table))):
                
                array_push($this->from, array('table' => $table, 'nickname' => $randomLetters));
                $find = true;
            endif;
        endwhile;
    }
    
    /**
     * Recupera o nickname da tabela dos apelidos contanstes no array $this->$nickname
     * @param String $tableName Nome da tabela a ser resgatado o nickname, apelido
     * @return Array->ArrayObject com nickname da tabela na consulta
     */
    private function getFrom($table)
    {
        $filter = new Filters\FilterArrayObject(new \ArrayObject($this->from), new \ArrayIterator(array('table')), $table);
        return $filter->getObjFiltered();
    }
}