<?php
class Context {
   // Public Properties
   var $Database;
   var $WarningCollector;
   var $ErrorManager;
   var $SqlCollector;
	var $ObjectFactory;
   var $SelfUrl;
   var $Querystring;
   var $Mode;              // Debug, Release, etc
   
	// Destructor (not called automatically thanks to php)
	function Unload() {
		if ($this->Database) $this->Database->CloseConnection();
		unset($this->Database);
		unset($this->WarningCollector);
		unset($this->ErrorManager);
		unset($this->SqlCollector);
      unset($this->ObjectFactory);
		unset($this->SelfUrl);
		unset($this->Querystring);
		unset($this->Mode);
	}
   
   // Constructor
   function Context() {
  		// Create an object factory
      $this->ObjectFactory = new ObjectFactory();

      // Current Mode
      $this->Mode = ForceIncomingCookieString("Mode", "");
		
      // Url of the current page
      $this->SelfUrl = basename(ForceString(@$_SERVER['PHP_SELF'], "index.php"));
      
      // Instantiate a SqlCollector (for debugging)
      $this->SqlCollector = new MessageCollector();
      $this->SqlCollector->CssClass = "Sql";
      
      // Instantiate a Warning collector (for user errors)
      $this->WarningCollector = new MessageCollector();
      
      // Instantiate an Error manager (for fatal errors)
      $this->ErrorManager = new ErrorManager();
      
      // Instantiate a Database object (for performing database actions)
      $this->Database = new MySQL(dbHOST, dbNAME, dbUSER, dbPASSWORD, $this);
   }
}
?>