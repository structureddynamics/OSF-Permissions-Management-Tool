<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\lister\AuthListerQuery;
  
  function listGroupUsers($groupURI, $credentials, $queryExtension = NULL) 
  {
    if(empty($groupURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('You would like to list the users of which group?');
      
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
    
    $authLister->getGroupUsers($groupURI)
               ->mime('resultset')
               ->send($queryExtension);
               
    if($authLister->isSuccessful())
    {
      $resultset = $authLister->getResultset()->getResultset();

      $groupUsers[$groupURI] = array();
      
      if(isset($resultset['unspecified'][key($resultset['unspecified'])]['http://www.w3.org/1999/02/22-rdf-syntax-ns#li']))
      {
        foreach($resultset['unspecified'][key($resultset['unspecified'])]['http://www.w3.org/1999/02/22-rdf-syntax-ns#li'] as $key => $user)
        {
          $groupUsers[$groupURI][] = $user['uri'];
        }
      }
      
      return($groupUsers);
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
  
  function showGroupUsers($users)
  {
    if(empty($users))
    {
      return;
    }
    
    cecho("\n\n", 'WHITE');

    $group = cecho(key($users), 'UNDERSCORE', TRUE);
    cecho("Users of the group: ".$group."\n\n", 'WHITE');
    
    foreach($users[key($users)] as $key => $userURI)
    {
      cecho("  (".($key + 1).") ".$userURI."\n", 'WHITE');
    }          

    cecho("\n\n", 'WHITE');
  }
         
?>
