<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
  
  include_once("getDatasets.php");
  
  function deleteDatasetAccesses($datasetURI, $credentials, $queryExtension = NULL) 
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
      
      $datasetNum = getInput('Which dataset would you like to delete {underscore}all{/underscore} the access permissions?');
      
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

    $yes = getInput('Are you sure you want to delete accesses of the "'.$datasetURI.'" dataset?', TRUE);
    
    if($yes === NULL)
    {
      $yes = FALSE;
    }      
    
    if($yes)
    {          
      $authRegistrarAccess = new AuthRegistrarAccessQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
      
      $authRegistrarAccess->deleteAll($datasetURI)
                          ->send($queryExtension);
                          
      if($authRegistrarAccess->isSuccessful())
      {
        cecho("\nAccess all access permissions delete!\n\n", 'green');
        
        exit(0);
      }
      else
      {
        $debugFile = md5(microtime()).'.error';
        file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarAccess, TRUE));
             
        @cecho('Couldn\'t delete all the access permissions'. $authRegistrarAccess->getStatusMessage() . 
             $authRegistrarAccess->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
        
        exit(1);
      } 
    }     
    
    exit(10);
  }
?>
