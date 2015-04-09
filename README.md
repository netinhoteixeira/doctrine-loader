# ![doctrine-loader](https://raw.githubusercontent.com/netinhoteixeira/doctrine-loader/master/resources/doctrine-loader.png) doctrine-loader

### Easy bootstraper for Doctrine ORM (independent of version)

If you have an old PHP 5.2 server, it's auto changes to Doctrine ORM 1.2.3, if you have a newest PHP 5.3 server, changes to Doctrine ORM 2.0.0.

#### Example

**config.inc.php**:
```php
<?php

// constants of connection
/*
define('DB_TYPE', 'mysql');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'estatistica');
define('DB_CHARSET', 'utf8');
*/

define('DB_TYPE', 'sqlite');
define('DB_NAME', dirname(__FILE__) . '/estatistica.sqlite');

define('WP_DEBUG', true);

require_once dirname(__FILE__) . '/library/doctrine-loader/doctrine.loader.php';
require_once dirname(__FILE__) . '/library/jsonfy/jsonfy.php';
?>
```

**estado.do.php**:
```php
<?php

header('Content-type: application/json');

require_once dirname(__FILE__) . '/config.inc.php';

_pcvd('estado_do_doctrine200', 'estado_do_doctrine123', DOCTRINE_NEWEST_PHP_VERSION);

function estado_do_doctrine200() {
    global $doctrine, $jsonfy;

    $EstadoTable = $doctrine->getEntityManager()->getRepository('Estado');

    if ($jsonfy->hasCallback()) {
        $retorno = array();
        $acao = isset($_GET['acao']) ? $_GET['acao'] : false;
        $sigla = isset($_GET['sigla']) ? strtoupper($_GET['sigla']) : false;

        if (!empty($sigla)) {
            $estado = $EstadoTable->findOneBy(array('sigla' => $sigla));

            if ($acao == 'ler') {
                if (!is_null($estado)) {
                    $retorno = array(
                        'sigla' => $estado->getSigla(),
                        'nome' => $estado->getNome(),
                        'populacao' => $estado->getPopulacao(),
                    );
                } else {
                    $retorno = array('erro' => 'not found');
                }
            } else {
                if (is_null($estado)) {
                    $estado = new Estado();
                }

                $estado->setSigla($sigla);
                $estado->setNome(isset($_GET['nome']) ? $_GET['nome'] : '');
                $estado->setPopulacao(isset($_GET['populacao']) ? $_GET['populacao'] : 0);

                try {
                    $doctrine->getEntityManager()->persist($estado);
                    $doctrine->getEntityManager()->flush();
                    $retorno = array('erro' => null);
                } catch (Exception $e) {
                    $retorno = array('erro' => $e->getMessage());
                }
            }
        } else {
            $retorno = array('erro' => 'sigla não fornecida');
        }

        $jsonfy->show($retorno);
    }
}

function estado_do_doctrine123() {
    global $jsonfy;

    $EstadoTable = EstadoTable::getInstance();

    if ($jsonfy->hasCallback()) {
        $retorno = array();
        $acao = isset($_GET['acao']) ? $_GET['acao'] : false;
        $sigla = isset($_GET['sigla']) ? strtoupper($_GET['sigla']) : false;

        if (!empty($sigla)) {
            $estado = $EstadoTable->findOneBySigla($sigla);

            if ($acao == 'ler') {
                if (!empty($estado->Sigla)) {
                    $retorno = array(
                        'sigla' => $estado->Sigla,
                        'nome' => $estado->Nome,
                        'populacao' => $estado->Populacao,
                    );
                } else {
                    $retorno = array('erro' => 'not found');
                }
            } else {
                if (empty($estado->Sigla)) {
                    $estado = new Estado();
                }

                $estado->Sigla = $sigla;
                $estado->Nome = isset($_GET['nome']) ? $_GET['nome'] : '';
                $estado->Populacao = isset($_GET['populacao']) ? $_GET['populacao'] : 0;

                try {
                    $estado->save();
                    $retorno = array('erro' => null);
                } catch (Doctrine_Connection_Exception $e) {
                    $retorno = array('erro' => $e->getPortableMessage());
                }
            }
        } else {
            $retorno = array('erro' => 'sigla não fornecida');
        }

        $jsonfy->show($retorno);
    }
}
?>
```

#### Downloads

* [**Test of Doctrine Loader 0.1** -  doctrine-loader-0.1-test.zip](https://drive.google.com/file/d/0B6XrugzJ_5IgT3RuczRLY0xUN1U/view?usp=sharing "A simple application to test the Doctrine Loader 0.1.")
* [**First version** - doctrine-loader-0.1.zip](https://drive.google.com/file/d/0B6XrugzJ_5IgS2tvNjZXNFlXa1E/view?usp=sharing "First version of bootstrapper.")
* [**Doctrine ORM 2.0.0 Manual** - doctrine-orm-manual-2-0-en.pdf](https://drive.google.com/file/d/0B6XrugzJ_5IgVXQ4akhXRU9kd0U/view?usp=sharing "Manual of Doctrine ORM 2.0.0.")
* [**Doctrine ORM 1.2.3 Manual** - doctrine-orm-manual-1-2-en.pdf](https://drive.google.com/file/d/0B6XrugzJ_5IgZnlGWkRkWTBRdWc/view?usp=sharing "Manual of Doctrine ORM 1.2.3.")
 
#### Links

* [Doctrine Project Blog](http://www.doctrine-project.org/blog)
* [Doctrine Project](http://www.doctrine-project.org/)
* [JSONfy](https://github.com/netinhoteixeira/jsonfy)

