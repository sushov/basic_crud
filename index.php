<?php
    class FootballClub
    {
        private $clubName = array();
                   
        public function setClubName($clubName)
        {
            $this->clubName['clubName'] = $clubName;
        }
         
        public function getClubName()
        {
            return $this->clubName['clubName'];
        }
         
        public function setEstablishedYear($ey)
        {
            $this->clubName['establishedYear'] = $ey;
        }
         
        public function getEstablishedYear()
        {
            return $this->clubName['establishedYear'];
        }

        public function setLeague($lg)
        {
            $this->clubName['league'] = $lg;
        }
         
        public function getLeague() 
        {
            return $this->clubName['league'];
        }
    }
 
    $footballClub = new FootballClub();
    $footballClub->setClubName("Chelsea Football Club");
    $footballClub->setEstablishedYear("1905");
     
    echo($footballClub->getClubName())."<br>";
    echo($footballClub->getEstablishedYear());
 
?>
