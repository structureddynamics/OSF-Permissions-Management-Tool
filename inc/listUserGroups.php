<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\lister\AuthListerQuery;
  
  function listUserGroups($userURI, $credentials, $queryExtension = NULL) 
  {
    $authLister = new AuthListerQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $userURI);
    
    $authLister->getUserGroups()
               ->mime('resultset')
               ->send($queryExtension);
               
    if($authLister->isSuccessful())
    {
      $resultset = $authLister->getResultset()->getResultset();
      
      $userGroups[$userURI] = array();

      if(isset($resultset['unspecified'][key($resultset['unspecified'])]['http://www.w3.org/1999/02/22-rdf-syntax-ns#li']))
      {
        foreach($resultset['unspecified'][key($resultset['unspecified'])]['http://www.w3.org/1999/02/22-rdf-syntax-ns#li'] as $key => $group)
        {
          $userGroups[$userURI][] = $group['uri'];
        }
      }
      
      return($userGroups);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authLister, TRUE));
           
      @cecho('Can\'t list the users of that group: '.$groupURI.' '. $authLister->getStatusMessage() . 
           $authLister->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
           
      exit(1);
    }
    
    return(array());
  }
  
  function showUserGroups($groups)
  {
    if(empty($groups))
    {
      return;
    }
    
    cecho("\n\n", 'WHITE');

    $user = cecho(key($groups), 'UNDERSCORE', TRUE);
    cecho("Groups of the user: ".$user."\n\n", 'WHITE');
    
    foreach($groups[key($groups)] as $key => $groupURI)
    {
      cecho("  (".($key + 1).") ".$groupURI."\n", 'WHITE');
    }          

    cecho("\n\n", 'WHITE');
  }
         
?>
