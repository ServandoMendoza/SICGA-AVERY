<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Products\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Products\Form\ProductsForm;

use Products\Model\ProductsCodeTable;
use Zend\View\Model\JsonModel;
use Products\Model\Products;

class ProductsController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $this->layout('layout/layout_app');
        $request = $this->getRequest();
        $form = new ProductsForm();


         if ($request->isPost()) {

            $Products = new Products($this->getServiceLocator());

            $form->setInputFilter($Products->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $Products->exchangeArray($form->getData());
               
                $this->getProductsTable()->saveProducts($Products);

                // Redirect to list of Products ny production model
                return $this->redirect()->toRoute('Products', array(
                    'action' => 'list'
                ));
            } 
        }

        return array(
            'form' => $form
        );
    }


    public function editAction()
    {
        $this->layout('layout/layout_app');
        $sku_id =  $this->params()->fromRoute('sku', 0);

        
        if (!$sku_id) {
             
            return $this->redirect()->toRoute('Products', array(
                'action' => 'list'
            ));
        }

        $productsDM = $this->getProductsTable()->getProductsBySku($sku_id);
       
        $form  = new ProductsForm();
        $form->bind($productsDM);
        $form->get('saveBtn')->setAttribute('value', 'Editar');

        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setInputFilter($productsDM->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {

                $this->getProductsTable()->saveProducts($form->getData());
                // Redirect to scrap list by production model
                
                return $this->redirect()->toRoute('Products', array(
                    'action' => 'list'
                ));
            }
        }

        return array(
            'form' => $form,
            'products' => $productsDM
        );
    }

     public function listAction()
    {
        $this->layout('layout/layout_app');
        $productsDM = $this->getProductsTable()->getProducts();

        return new ViewModel(array(
            'productsList' => $productsDM,
        ));
    }

    public function deleteProductAjaxAction()
    {
        $request = $this->getRequest();
        $id = $request->getPost('id');

        // Eliminar relaciones hacia scrap y tiempo muerto.
        $this->getProductsTable()->deleteProduct((int)$id);

        return new JsonModel(
            array(
                "result" => 1
            )
        );
    }

    public function jsonListAction()
    {
        $config = $this->getServiceLocator()->get('Config');

        $table = 'product_vw';
        $primaryKey = 'sku';
        $sql_details = array(
            'user' => $config['db']['username'],
            'pass' => $config['db']['password'],
            'db'   => $config['db']['database'],
            'host' => $config['db']['host']
        );
        $columns = array(
            array( 'db' => 'sku', 'dt' => 0 ),
            array( 'db' => 'description',  'dt' => 1 ),
            array( 'db' => 'size',   'dt' => 2 ),
            array( 'db' => 'create_date',   'dt' => 3 )
        );

        // Create an empty array for the encoded resultset
        $rows = array();

        $rs = $this->getProductsTable()->getListJson($_GET, $sql_details, $table, $primaryKey, $columns);

        foreach($rs['data'] as $r)
        {
            $rows[] =  array_map('utf8_encode', $r);
        }

        $rs['data'] =$rows;

        // Remove View return
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContent($_GET['callback'].'('.json_encode($rs).');');

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'text/html');

        return $response;
    }

    public function getProductsTable()
    {   
       
        if (!$this->ProductsTable) {
            $sm = $this->getServiceLocator();
            $this->ProductsTable = $sm->get('Products\Model\ProductsTable');
        }
       
        return $this->ProductsTable;
    }



    
}

