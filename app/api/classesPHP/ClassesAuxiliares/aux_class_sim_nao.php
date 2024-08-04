<?php


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classesErros
 *
 * @author darksp
 */
class aux_class_sim_nao extends Class_Valida_Dados {

    //classes estrangeiras

    private $Class_sim_nao;
    private $db;
    private $Class_Sessao;
    private $aux_class_gerencia_permissao;

    //construtor da classe
    public function __construct($db) {
        //banco
        $this->db = $db;
        $this->Class_sim_nao = new Class_sim_nao($this->db);
        $this->aux_class_gerencia_permissao = new aux_class_gerencia_permissao($this->db);
        //verifica se tem permissao para esta pagina
        $this->aux_class_gerencia_permissao->Verifica_Permissao();

        //classes estrangeiras
        $this->Class_Sessao = new Class_Sessao();
        $this->Controle();
    }

    /**
     * getChaves($get=true) funcao responsavel por retornar as chaves ou parametros gets de chaves estrangeiras
     * retorna uma string para url do tipo get caso get seja true
     * ou retorna uma string com inputs para formulario caso o get seja false
     * @param type $get padrao true, retorna string get, false retorna lista de inputs
     * @return string retorna inputs com os ids das chaves estrangeiras
     */
    private function getChaves($get = true) {
        //$ids array de classes nome da classe e id da classe
        $ids['Class_sim_nao'] = 'idsim_nao';

        $list_ids = '';
        foreach ($ids as $class => $id) {
            $classe = $class; //recebe o nome da classe a se conusltada
            $metodo = 'get' . $id; //recebe o nome do metodo a ser consultado
            if ($get) {
                if (isset($_REQUEST["$id"])) {
                    if (empty($_REQUEST["$id"])) {
                        //no caso da chamada após um insert a chave será a da própria classe inserida nesse caso, pega o id da classe
                        if ($class == 'Class_sim_nao') {
                            $list_ids.= '&' . $id . '=' . $this->$classe->$metodo();
                        }
                    } else {
                        $list_ids.= '&' . $id . '=' . $this->Proteje_Sql($_REQUEST["$id"]);
                    }
                }
            } else {
                //consulta se a classe é a principal da página se for não é necessário gerar inpust pois já existe por padrão nos formularios
                if ($class != 'Class_sim_nao') {

                    $list_ids.='<input name="' . $id . '" type="hidden" value="' . $this->$classe->$metodo() . '"/>';
                }
            }
        }
        // echo $list_ids;
        return $list_ids;
    }

    function Controle() {
        $ACAO = "";
        if (isset($_REQUEST["ACAO"])) {
            $ACAO = $this->proteje_sql($_REQUEST["ACAO"], "");
        }
        switch ($ACAO) {

            // INICIA O FORMULARIO PARA CADASTRO
            //############### INICIO CASE FORM_CADASTRO
            case "NEW_FORM":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'INSERT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                $this->setTabs('sim_nao_functions.php?ACAO=FORM' . $this->getChaves(true) . '', ' sim_nao', 'Define resposta sim ou não para algum campo da tabela solicitante');
                $this->setConteudo($this->getTabs());

                break;
            case "FORM":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'INSERT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }

                $this->setConteudo($this->MontaForm($tipo = "cadastro", "<h2>Utilize o formulário abaixo para inserir um novo registro</h2>"));
                break;
            case "NEW_BUSCA":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'INSERT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }

                break;
            case "INSERT":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'INSERT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->PegaPostssim_nao(false)) {
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->salvasim_nao(false)) {
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }

                $this->setConteudo('<script>parent.regarregaTabs(\'form_sim_nao\',\'sim_nao_functions.php?ACAO=TABS_LOAD' . $this->getChaves(true) . '\');</script>');
                $this->setConteudo('<script>parent.new_cx_alerta(\'Registro inserido com sucesso.\');</script>');
                break;
            case "TABS_LOAD":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'SELECT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                $this->Carregasim_nao();
                $this->setTabs('sim_nao_functions.php?ACAO=LOAD' . $this->getChaves(true), 'sim_nao', 'Define resposta sim ou não para algum campo da tabela solicitante');


                $this->setConteudo($this->getTabs());
                break;

            case "LOAD":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'SELECT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->Carregasim_nao()) {
                    $this->setErros('<h2>Atenção!</h2>Não registro não localizado, certifique se o registro naõ foi removido atualizando a lista.');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                $this->setConteudo($this->MontaForm("edita", "<h2>Utilize o formulário para editar o registro</h2>"));
                break;
            case "UPDATE":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'UPDATE')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->Carregasim_nao()) {
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->PegaPostssim_nao(true)) {
                    $this->setConteudo($this->MontaForm("edita", "<h2>Utilize o formulário para editar o registro</h2>"));
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->salvasim_nao(true)) {
                    $this->setConteudo($this->MontaForm("edita", "<h2>Utilize o formulário para editar o registro</h2>"));
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }

                $this->setConteudo($this->MontaForm("edita", "<h2>Utilize o formulário para editar o registro</h2>"));

                $this->setConteudo('<script>parent.new_cx_alerta(\'Registro atualizado com sucesso.\');</script>');
                break;
            case "DELETE":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'DELETE')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->Carregasim_nao()) {
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                if (!$this->Excluirsim_nao()) {
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    $this->setConteudo($this->Menus());
                    $this->ListaAuxliar();
                    break;
                }
                $this->setConteudo($this->Menus());
                $this->ListaAuxliar();
                $this->setConteudo('<script>parent.new_cx_alerta(\'Registro removido com sucesso.\');</script>');
                break;
            case "LIST":
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'SELECT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                $this->setConteudo($this->Menus());
                $this->ListaAuxliar();
                break;
            default:
                if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'SELECT')) {
                    $this->setErros('<h2>Atenção!</h2><p>Você não tem permissão para efetuar esta ação.</p>');
                    $this->setConteudo('<script>parent.new_cx_alerta(\'' . $this->getErros() . '\');</script>');
                    break;
                }
                $this->setTabs('sim_nao_functions.php?ACAO=LIST' . $this->getChaves(true), 'sim_nao', 'Aba principal: Define resposta sim ou não para algum campo da tabela solicitante');
                $this->setConteudo($this->getTabs());
                break;
        }
    }

    private function Menus() {
        if (!$this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'INSERT')) {
            return;
        }
        return '<div class="menucontent">  
                <form id="newcad" action="sim_nao_functions.php" method="post">                                
                        <button class="btsprincipais" type="button" title="Clique aqui para inserir um novo registro.">Novo Registro</button>
                        <input name="ACAO" type="hidden" value="NEW_FORM"/>
                        ' . $this->getChaves(false) . '
                    </form>
                    <form id="newbusca" action="sim_nao_functions.php" method="post">                                
                        <button class="btatualiza" type="button" title="Clique aqui para atualizar a lista de registros.">Atualizar Lista</button>
                        <input name="ACAO" type="hidden" value="LIST"/>
                        ' . $this->getChaves(false) . '
                    </form>
                    
                </div>';
    }

    private function Carregasim_nao() {
        if (!isset($_REQUEST["idsim_nao"])) {
            return false;
        }
        $idsim_nao = $this->Proteje_Sql($_REQUEST["idsim_nao"], "");

        if (!$this->Class_sim_nao->carregasim_nao($idsim_nao)) {
            return false;
        }
        return true;
    }

    /**
     * Funcao CamposForm - responsável por devolver os campos do formulario            
     */
    function CamposForm() {

        $campos = '
                               <input   class="numerico"   type="hidden" id="idsim_nao" name="idsim_nao" value="' . $this->Class_sim_nao->getidsim_nao() . '" maxlength="11" size="32" />
                              
                                    <div>
                                            <label>
                    <span>Valor</span>
                    <span class="ui-icon ui-icon-comment" title=""></span>
                  </label>	
                                                <input     type="text" id="valor" name="valor" value="' . $this->Class_sim_nao->getvalor() . '" maxlength="4" size="32" />
                                    </div>' . $this->getChaves(false);

        return $campos;
    }

    private function MontaForm($tipo = "cadastro", $mensagem = "") {
        $legenda = "";
        $acao = "";
        $bt = "";
        //VERIFICA O TIPO DO FORM, SE É PARA EDICAO OU CADASTRO
        if ($tipo == "edita") {
            $legenda = 'Edição de sim_nao';
            if ($this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'UPDATE')) {
                $acao = '<input type="hidden" name="idsim_nao" value="' . $this->Class_sim_nao->getidsim_nao() . '" />
                     <input type="hidden" name="ACAO" value="UPDATE" />';
                $bt = '<input class="bt_editar" name="BT_SALVAR" type="submit" id="BT_SALVAR" value="Salvar Dados"  />';
            }
        } else {
            $legenda = 'Cadastro de sim_nao';
            if ($this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'INSERT')) {
                $acao = '<input type="hidden" name="ACAO" value="INSERT" />';
                $bt = '<input class="bt_salvar" name="BT_SALVAR" type="submit" id="BT_SALVAR" value="Salvar Dados"  />';
            }
        }

        $NEW_FORM = '			
                <form enctype="multipart/form-data" id="form_sim_nao" name="form_sim_nao" method="post" class="formulario"  action="sim_nao_functions.php" >
                    <fieldset>
                        <legend>' . $legenda . '</legend>
                        <div class="divgrande">' . $mensagem . '</div>	
                                    ' . $this->CamposForm() . '	
                        <div class="botoes">
                        ' . $acao . '
                        ' . $bt . '
                        </div>
                    </fieldset>
                </form>';
        //FIM DO FORM EDITA			
        //$this->setConteudo($NEW_FORM);
        return $NEW_FORM;
    }

    function PegaPostssim_nao() {
        //trata o post do campo

        if (isset($_REQUEST["valor"])) {
            $this->Class_sim_nao->setvalor($this->Proteje_Titulo($_REQUEST["valor"], "<li>O campo Valor deve ser preenchido</li>", ""));
        }
        //testa e verifica se ocorreu erros
        if ($this->getErros()) {
            return false;
        }

        return true;
    }

    /**
     * funcao salvaRegistro responsável por salvar ou editar o registro,
     * recebe como parametro de comparacao o borlean $edita e se for true é para fazer um update
     * em caso de falso será feito um insert
     * @param $edita bolean
     */
    function salvasim_nao($edita = false) {
        //inicia a transacao
        $this->db->transacao();
        $extra = "";
        //se edita for true atualiza os dados para edicao do registro
        //do contrário efetua um novo insert
        if ($edita) {

            if (!$this->Class_sim_nao->atualizasim_nao()) {
                $this->setErros("<h2>Atenção!</h2><p>Ocorreu um erro ao atualizar o registro na tabela sim_nao .</p>");
                $this->setErros("<p>" . $this->Class_sim_nao->getResp() . "</p>");

                //efetua um rollback
                $this->db->rollback();
                return false;
            }
        } else {

            if (!$this->Class_sim_nao->inserirsim_nao()) {
                $this->setErros("<h2>Atenção!</h2><p>Ocorreu um erro ao inserir o novo registro na tabela sim_nao.</p>");
                $this->setErros("<p>" . $this->Class_sim_nao->getResp() . "</p>");
                //efetua um rollback
                $this->db->rollback();
                return false;
            }
        }
        //salva o arquivo caso exista
        //finaliza a transacao
        if (!$this->db->getErro()) {
            //efetua o rolback se caso ocorreu um erro
            $this->db->commit();
            return true;
        } else {
            $this->db->rollback();
            return false;
        }
    }

    private function Excluirsim_nao() {

        if (!$this->Class_sim_nao->deletasim_nao()) {
            $this->setErros("<h2>Atenção! O registro '" . $this->Class_sim_nao->getvalor() . "' não pode ser removido.</h2>");
            $this->setErros("<p>$this->Class_sim_nao->getResp()</p>");
            return false;
        }
        //$this->setConteudo("<h2>Registro removido com sucesso '".$this->Class_sim_nao->getvalor()."'.</h2>");
        return true;
    }

    private function ListaAuxliar() {

        $parametros_extras = '';
        $consulta_extra = '';
        $ordem_extra = ''; //ordenacao pelos links     
        //cria a query de consulta atribuindo $consulta_extra caso exista
        $sql = ' where 1=1  ' . $consulta_extra;

        //INSERE A ACAO LISTA PARA NOVAS BUSCAS
        $parametros_extras = '&ACAO=LIST';

        //instancia a classe de paginacao
        $Class_paginacao = new Class_paginacao();
        $inicial = $Class_paginacao->getRegistroInicial(); // pega a posicao inicial de conuslta
        $numreg = $Class_paginacao->getNumeroRegistro(); //pega a posicao final de consulta
        //pega a ordem passando a coluna e ordem padrao de ordenacao
        $ordem_extra = $Class_paginacao->getOrdemDB('idsim_nao', 'asc');
        //######### FIM dados Paginação

        if (!$this->Class_sim_nao->listasim_nao($sql . $ordem_extra . " LIMIT $inicial,  $numreg")) {
            $this->setConteudo('<h2>Não existem registros cadastrados para "sim_nao".</h2>');
        }
        // Serve para contar quantos registros você tem na seua tabela para fazer a paginação
        $quantreg = $this->Class_sim_nao->retornaQuantidadeRegistrossim_nao($sql);
        $Class_paginacao->setQuantidadeRegistros($quantreg);
        /*
          if ($asc == 'asc') {
          $parametros_extras .= '&asc=desc';
          } else {
          $parametros_extras .= '&asc=asc';
          } */
        $Class_paginacao->setParametrosExtras($parametros_extras);
        //$link_ordem = $Class_paginacao->getUrlCompleta() . '&or=';
        $UrlCompleta = $Class_paginacao->getUrlCompleta();
        $paginacao = $Class_paginacao->getPaginacao();
        $this->setConteudo($this->MontaLista($this->Class_sim_nao->getResp(), '<h2>Listagem de "sim_nao".</h2>', $UrlCompleta) . $paginacao);
        return true;
    }

    function MontaLista($array = '', $mensagem = '', $link_ordem = '') {
        $lista = "";
        $n = count($array);
        $obj = new Class_sim_nao();
        for ($i = 0; $i < $n; $i++) {
            $obj = $array[$i];

            $bt_exclui = "";
            $bt_edita = "";
            if ($this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'DELETE')) {
                $bt_exclui = '<form id="form_exclui_x' . $obj->getidsim_nao() . '" action="sim_nao_functions.php" method="post">					
                                            <a class="bt_delete" href="#"><img src="img/bt_exclui.png" width="25" height="30" title="Excluir Registro"  alt="Excluir" /></a>													
                                            <input name="ACAO" type="hidden" value="DELETE" />
                                            <input name="idsim_nao" type="hidden" value="' . $obj->getidsim_nao() . '" />
                                            ' . $this->getChaves(false) . '
                                    </form>';
            }
            if ($this->aux_class_gerencia_permissao->Acoes_Form('sim_nao', 'SELECT')) {
                $bt_edita = '<form id="form_edita_x' . $obj->getidsim_nao() . '" action="sim_nao_functions.php" method="post">					
                                            <a  class="bt_visualiza" href="#"><img src="img/bt_edita.png" width="25" height="30" title="Editar Registro"  alt="Editar" /></a>													
                                            <input name="ACAO" type="hidden" value="TABS_LOAD" />
                                            <input name="idsim_nao" type="hidden" value="' . $obj->getidsim_nao() . '" />
                                            ' . $this->getChaves(false) . '
                                    </form>';
            }
            $lista .= '<tr class="linha_off" onmouseover="mudar_cor_over(this)" onmouseout="mudar_cor_out(this)" >
                                                
                                       <td align="center">' . $obj->getidsim_nao() . '</td>
                                       <td align="center">' . $obj->getvalor() . '</td>
                                       <td align="center">' . $bt_edita . '</td>
                                       <td align="center">' . $bt_exclui . '</td>
                                       
                                          </tr>';
        }
        $cabecalho_tabela = '
                                                       <tr>
                                                           
                                       <th align="center"><a title="Ordenar por Id" class="ordem" href="' . $link_ordem . 'idsim_nao"><span class="ui-icon ui-icon-carat-2-n-s"></span>Id</a></th>
                                       <th align="center"><a title="Ordenar por Valor" class="ordem" href="' . $link_ordem . 'valor"><span class="ui-icon ui-icon-carat-2-n-s"></span>Valor</a></th>
                                       <th align="center"></th>
                                       <th align="center"></th>
                                       
                                                        </tr>';
        $tabela = $mensagem . '<table class="tablistaitens  bottomSpace ui-widget ui-widget-content ui-corner-all fullBox" width="100%" >
                                            ' . $cabecalho_tabela . '
                                            ' . $lista . '
                                    </table>';
        //$this->setConteudo($tabela);
        return $tabela;
    }

}

?>
         