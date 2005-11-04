 
CREATE TABLE `privacy_confirmations` (
  `id` int(11) NOT NULL auto_increment,
  `owner` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `state` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `owner` (`owner`,`state`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;
-- 
-- Table structure for table `other_data`
-- 

-- 
-- Table structure for table `other_data`
-- 

CREATE TABLE `other_data` (
  `id` bigint(20) NOT NULL default '0',
  `lang` char(2) NOT NULL default 'en',
  `name` varchar(32) NOT NULL default '',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `other_data`
-- 

INSERT INTO `other_data` VALUES (0, 'en', 'privacy_confirmation_text', 'Informativa ai sensi dell?art. 13 del D.Lgs. 196/2003\r\n\r\nL''associazione Business Club Milan IN (il ?Club?), al fine di conformarsi al precetto dell?art. 13 del D. Lgs. 196/2003 "Codice in materia di protezione dei dati personali? (il ?Decreto?) informa che:\r\n\r\n1) i trattamenti a cui saranno sottoposti i Vostri dati personali, richiesti ed acquisiti, sono diretti esclusivamente all?espletamento da parte del Club degli scopi associativi evidenziati nello statuto del Club. Tali dati saranno conservati presso il Club per la durata della vostra iscrizione al Club;\r\n\r\n2) il trattamento sarà effettuato prevalentemente con l?ausilio di mezzi elettronici o comunque automatizzati e può consistere in qualunque operazione o complesso di operazioni tra quelle indicate all?art. 4, comma 1, lettera a), del Decreto;\r\n\r\n3) il conferimento dei dati personali è facoltativo, ma costituisce "conditio sine qua non" per essere soci del Club e pertanto l?eventuale rifiuto a corrispondere i Vostri dati può comportare per il Club l?impossibilità di dar seguito alla Vostra iscrizione.\r\n\r\n4) i dati personali possono essere comunicati, per le medesime finalità di cui al punto 1), a pubbliche amministrazioni ai sensi di legge, nonché a soggetti terzi per la fornitura di servizi di vario tipo (contabilità, spedizione di corrispondenza, servizi bancari, corrispondenti esteri, ecc);\r\n\r\n5) i Vostri dati personali non saranno soggetti a diffusione;\r\n\r\n6) il Titolare del trattamento è l''associazione Business Club Milan IN, Via Tanaro, 22 - 20128 Milano. Responsabile del trattamento dei dati è il Presidente del Club;\r\n\r\n7) potrete rivolgerVi al Titolare o al Responsabile summenzionati per far valere i Vostri diritti,così come previsto dall?art. 7 del D. Lgs. 196/2003 il cui testo è di seguito interamente riportato.\r\n\r\nArt. 7 D. Lgs. 196/2003\r\nDiritto di accesso ai dati personali ed altri diritti\r\n\r\n1. L''interessato ha diritto di ottenere la conferma dell''esistenza o meno di dati personali che lo riguardano, anche se non ancora registrati, e la loro comunicazione in forma intelligibile.\r\n2. L?interessato ha diritto di ottenere l?indicazione:\r\na) dell?origine dei dati personali;\r\nb) delle finalità e modalità del trattamento;\r\nc) della logica applicata in caso di trattamento effettuato con l?ausilio di strumenti elettronici;\r\nd) degli estremi identificativi del titolare, dei responsabili e del rappresentante designato ai sensi dell?articolo 5, comma 2;\r\ne) dei soggetti o delle categorie di soggetti ai quali i dati personali possono essere comunicati o che possono venirne a conoscenza in qualità di rappresentante designato nel territorio dello Stato, di responsabili o incaricati.\r\n3. L?interessato ha diritto di ottenere:\r\na) l''aggiornamento, la rettificazione ovvero, quando vi ha interesse, l''integrazione dei dati;\r\nb) la cancellazione, la trasformazione in forma anonima o il blocco dei dati trattati in violazione di legge, compresi quelli di cui non è necessaria la conservazione in relazione agli scopi per i quali i dati sono stati raccolti o successivamente trattati;\r\nc) l''attestazione che le operazioni di cui alle lettere a) e b) sono state portate a conoscenza, anche per quanto riguarda il loro contenuto, di coloro ai quali i dati sono stati comunicati o diffusi, eccettuato il caso in cui tale adempimento si rivela impossibile o comporta un impiego di mezzi manifestamente sproporzionato rispetto al diritto tutelato.\r\n4. L?interessato ha diritto di opporsi, in tutto o in parte:\r\na) per motivi legittimi al trattamento dei dati personali che lo riguardano, ancorché pertinenti allo scopo della raccolta;\r\nb) al trattamento di dati personali che lo riguardano a fini di invio di materiale pubblicitario o di vendita diretta o per il compimento di ricerche di mercato o di comunicazione commerciale. ');
