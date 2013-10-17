<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\lister\AuthListerQuery;
  
  function listGroups($credentials, $queryExtension = NULL) 
  {
    $authLister = new AuthListerQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
    
    $authLister->getGroups()
               ->mime('resultset')
               ->send($queryExtension);
               
    if($authLister->isSuccessful())
    {
      $resultset = $authLister->getResultset()->getResultset();

      $groupsByAppID = array();
      
      foreach($resultset['unspecified'] as $groupURI => $group)
      {
        $appID = $group['http://purl.org/ontology/wsf#appID'][0]['value'];
        
        if(!isset($groupsByAppID[$appID]))
        {
          $groupsByAppID[$appID] = array();
        }
        
        $groupsByAppID[$appID][] = $groupURI;
      }
      
      return($groupsByAppID);      
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authLister, TRUE));
           
      @cecho('Can\'t list groups'. $authLister->getStatusMessage() . 
           $authLister->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
           
      exit(1);
    }
    
    return(array());
  }
  
  function showGroups($groups)
  {
    if(empty($groups))
    {
      return;
    }
    
    cecho("\n\n", 'WHITE');

    $nb = 1;
    
    foreach($groups as $appID => $groupURIs)
    {
      $appID = cecho($appID, 'UNDERSCORE', TRUE);
      cecho("Application ID: ".$appID."\n\n", 'WHITE');
      
      foreach($groupURIs as $group)
      {
        cecho("  (".$nb.") ".$group."\n", 'WHITE');
        
        $nb++;
      }
      
      cecho("\n\n", 'WHITE');
    }      
  }  
?>
