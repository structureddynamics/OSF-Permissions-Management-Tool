<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
  use \StructuredDynamics\osf\php\api\ws\auth\lister\AuthListerQuery;
  use \StructuredDynamics\osf\php\api\framework\CRUDPermission;
  
  include_once("getDatasets.php");
  
  function createAccess($datasetURI, $groupURI, $crud, $webservices, $credentials, $queryExtension = NULL) 
  {
    if(empty($datasetURI))
    {
      $datasets = getDatasets($credentials);
      
      if(empty($datasets))
      {
        cecho("\nThere are currently no dataset in the OSF instance\n\n", 'YELLOW');
        
        exit(1);
      }
      
      showDatasets($datasets);         
      
      $datasetNum = getInput('Which dataset would you like to give access to?');

      $nb = 1;
      foreach($datasets as $dsets)
      {
        foreach($dsets as $key => $dataset)
        {
          if($datasetNum == $nb)
          {
            $datasetURI = $dataset['uri'];
            break;
          }
          
          $nb++;
        }
        
        if($datasetNum == $nb)
        {
          break;
        }
      }
    }
    
    if(empty($groupURI))
    {
      $groups = listGroups($credentials, $queryExtension);
      
      if(empty($groups))
      {
        cecho("\nThere are currently no group in the OSF instance\n\n", 'YELLOW');
        
        exit(1);
      }      
      
      showGroups($groups);  
      
      $groupNum = getInput('Which group would you like to give access to the selected dataset?');
      
      $nb = 1;
      foreach($groups as $appID => $grps)
      {
        foreach($grps as $group)
        {
          if($groupNum == $nb)
          {
            $groupURI = $group;
          }
          
          $nb++;
        }
      }          
    }
    
    if(empty($crud))
    {
      cecho("\n");
      $create = getInput('Do you want to give {underscore}Create{/underscore} permissions to that group, for that dataset?', TRUE);
      $read = getInput('Do you want to give {underscore}Read{/underscore} permissions to that group, for that dataset?', TRUE);
      $update = getInput('Do you want to give {underscore}Update{/underscore} permissions to that group, for that dataset?', TRUE);
      $delete = getInput('Do you want to give {underscore}Delete{/underscore} permissions to that group, for that dataset?', TRUE);
      cecho("\n");
      
      $crud = array(
        (is_null($create) ? FALSE : $create),
        (is_null($read) ? FALSE : $read),
        (is_null($update) ? FALSE : $update),
        (is_null($delete) ? FALSE : $delete),
      );
    }
    
    if($webservices == 'all' || empty($webservices))
    {
      // Get the list of registered web services
      $authLister = new AuthListerQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
      
      $authLister->getRegisteredWebServiceEndpointsUri()
                 ->mime('resultset')
                 ->send($queryExtension);
      
      if(!$authLister->isSuccessful())      
      {
        $debugFile = md5(microtime()).'.error';
        file_put_contents('/tmp/'.$debugFile, var_export($authLister, TRUE));
             
        @cecho('Can\'t get the list of registered web services to create the permissions for: '.$arguments['uri'].'. '. $authLister->getStatusMessage() . 
             $authLister->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
        
        exit(1);
      } 
      
      $webservices = array();
      
      $resultset = $authLister->getResultset()->getResultset();
      
      foreach($resultset['unspecified'] as $list)
      {
        foreach($list['http://www.w3.org/1999/02/22-rdf-syntax-ns#li'] as $ws)
        {
          $webservices[] = $ws['uri'];
        }
      }      
    }
    else
    {
      $webservices = explode(';', $webservices);
    }
  
    $crud = new CRUDPermission($crud[0], $crud[1], $crud[2], $crud[3]);
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
    
    $authRegistrarAccess->create($groupURI, $datasetURI, $crud, $webservices)
                        ->send($queryExtension);
    
    if($authRegistrarAccess->isSuccessful())
    {
      cecho("\nAccess permission created!\n\n", 'green');
      
      exit(0);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarAccess, TRUE));
           
      @cecho('Couldn\'t create the access permission'. $authRegistrarAccess->getStatusMessage() . 
           $authRegistrarAccess->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
      
      exit(1);
    }
    
    exit(10);
  }
?>
