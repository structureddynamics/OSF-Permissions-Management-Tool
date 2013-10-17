<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\lister\AuthListerQuery;
  
  function listGroupPermissions($groupURI, $credentials, $queryExtension = NULL) 
  {
    if(empty($groupURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('You would like to list the permissions of which group?');
      
      $nb = 1;
      
      foreach($groupsByAppID as $appID => $groups)
      {
        foreach($groups as $group)
        {
          if($groupNum == $nb)
          {
            $groupURI = $group;
            break;
          }
          else
          {
            $nb++;
          }
        }
        
        if(!empty($groupURI))
        {
          break;
        }
      }
    }    
    
    $authLister = new AuthListerQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
    
    $authLister->getGroupAccesses($groupURI)
               ->mime('resultset')
               ->send($queryExtension);

    if($authLister->isSuccessful())
    {
      $resultset = $authLister->getResultset()->getResultset();
      
      if(isset($resultset['unspecified']))
      {
        return(array($groupURI => $resultset['unspecified']));
      }
      else
      {
        return(array());
      }
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authLister, TRUE));
           
      @cecho('Can\'t list the permissions of that group: '.$groupURI.' '. $authLister->getStatusMessage() . 
           $authLister->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
           
      exit(1);
    }
    
    return(array());
  }
  
  function showGroupPermissions($permissions)
  {
    if(empty($permissions))
    {
      return;
    }
    
    cecho("\n\n", 'WHITE');

    $group = cecho(key($permissions), 'UNDERSCORE', TRUE);
    cecho("Permissions of the group: ".$group."\n\n", 'WHITE');
        
    $nb = 1;
    
    foreach($permissions[key($permissions)] as $accessURI => $access)
    {
      cecho("  (".$nb.") ", 'WHITE');
      
      $value = cecho($access['http://purl.org/ontology/wsf#datasetAccess'][0]['uri'], 'CYAN', TRUE);
      
      if($nb < 10)
      {
        cecho("On Dataset:                    ".$value."\n", 'WHITE');  
      }
      else
      {
        cecho("On Dataset:                   ".$value."\n", 'WHITE');
      }
      
      cecho("      With CRUD Permissions: ", 'WHITE');
      $value = cecho(strtoupper($access['http://purl.org/ontology/wsf#create'][0]['value']), 'CYAN', TRUE);
      cecho("Create: ".$value."\n", 'WHITE');
      $value = cecho(strtoupper($access['http://purl.org/ontology/wsf#read'][0]['value']), 'CYAN', TRUE);
      cecho("                             Read:   ".$value."\n", 'WHITE');
      $value = cecho(strtoupper($access['http://purl.org/ontology/wsf#update'][0]['value']), 'CYAN', TRUE);
      cecho("                             Update: ".$value."\n", 'WHITE');
      $value = cecho(strtoupper($access['http://purl.org/ontology/wsf#delete'][0]['value']), 'CYAN', TRUE);
      cecho("                             Delete: ".$value."\n", 'WHITE');
      cecho("      Using web service endpoints:   ", 'WHITE');

      foreach($access['http://purl.org/ontology/wsf#webServiceAccess'] as $key => $webservice)
      {
        $pos = strrpos($webservice['uri'], '/wsf/ws/');
        
        cecho(substr($webservice['uri'], $pos + 4), 'CYAN');

        if(($key + 1) < count($access['http://purl.org/ontology/wsf#webServiceAccess']))
        {
          cecho(', ', 'WHITE');
        }
        
        if(($key > 1) && (($key + 1) % 3 == 0))
        {
          echo "\n                                     ";
        }
      }
      
      echo "\n";
      
      $nb++;
      cecho("\n", 'WHITE');
    }          

    cecho("\n\n", 'WHITE');
  }
         
?>
