<?php
class FME_Events_Helper_Calendar extends Mage_Core_Helper_Abstract
{
    const ALL_EVENTS_ENBL = 'events_options/calendar_configs/all_events_link';
    const LIMIT_EVENTS = 'events_options/calendar_configs/limit_events_popup';
    /* draws a calendar */
    public function drawCalendar($month,$year)
    {
        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="fme-calendar">';
    
        /* table headings */
        $headings = array('Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
        $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';
    
        /* days and weeks vars now ... */
        $running_day = date('w',mktime(0,0,0,$month,1,$year));
        $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();
    
        /* row for week one */
        $calendar.= '<tr class="calendar-row">';
    
        /* print "blank" days until the first of the current week */
        for($x = 0; $x < $running_day; $x++):
            $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
            $days_in_this_week++;
        endfor;
    
        /* keep going with days.... */
        for($list_day = 1; $list_day <= $days_in_month; $list_day++):
            $calendar.= '<td class="calendar-day">';
            /* add in the day number */
            $dateString = date('Y-m-d',strtotime($year.'-'.$month.'-'.$list_day));
            $calendar.= '<div class="day-number"><a href="'.Mage::helper('events')->customUrl($dateString).'">'.$list_day.'</a></div>';
            $title = '&nbsp;';
            $links = '';
            $pfx = '';
            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            if ($evtData = $this->eventOfDate($dateString))
            {
                $calendar .= '<div class="test" id="testG-'.$list_day.'" style="cursor: pointer"><ol>';
				$links .='<ul>';
                
                foreach ($evtData as $e)
                {	
					
                    $calendar .= '<li><span>'.$e['event_title'].'</span></li>';
                    $pfx = $e['event_url_prefix'];
                    $title = $e['event_title'];
					/* mouad */
					$kategory = $e['kategory'];
                    $links .= stripslashes("<li><span><a href=".Mage::helper('events')->customUrl($pfx).">".$title."</a><p>(<b>From:</b> ".date('d M, Y - H:i ',strtotime($e['event_start_date']))." <b>To:</b> ".date('d M, Y - H:i ',strtotime($e['event_end_date'])).")</p><p><strong>Kategory:</strong>".$kategory."</span></li>");
                }
                if (Mage::getStoreConfig(self::ALL_EVENTS_ENBL))
                {
                    $links .= stripslashes("<br/><a href=".Mage::helper('events')->customUrl().">All Events</a>");
                }
				$links .='</ul>';
                $calendar .= "</ol></div>
					<script type='text/javascript'>
                        Opentip.styles.myStyle = {
                            className: 'slick',
                            showOn: 'click',
                            hideOn: 'null',
                            stem: true,
                            stemSize: 15,
                            delay: false,
                            tipJoint: [ 'left', 'top' ],
                            target: true,
                            targetJoint: [ 'right', 'top' ],
                            title: 'Event(s) in ".date('d M, Y',strtotime($year.'-'.$month.'-'.$list_day))."',
                            showEffect: 'blindDown'
                        };
                
                        Tips.add('testG-".$list_day."', '".$links."', { style: 'myStyle' });
                    </script>";
                    
            }
            else
            {
                $calendar.= '<p>&nbsp;</p>';
            }
            
		
            $calendar.= '</td>';
            if($running_day == 6):
                $calendar.= '</tr>';
                if(($day_counter+1) != $days_in_month):
                    $calendar.= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++; $running_day++; $day_counter++;
        endfor;
    
        /* finish the rest of the days in the week */
        if($days_in_this_week < 8):
            for($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar.= '<td class="calendar-day-np">&nbsp;</td>';
            endfor;
        endif;
    
        /* final row */
        $calendar.= '</tr>';
    
        /* end the table */
        $calendar.= '</table>';
        
        /* all done, return result */
        return $calendar;
    }
    
    public function calenderControls()
    {
        /* date settings */
        $month = (int) (@$_GET['month'] ? @$_GET['month'] : date('m'));
        $year = (int)  (@$_GET['year'] ? @$_GET['year'] : date('Y'));
        /* select month control */
        $select_month_control = '<select name="month" id="month">';
        for($x = 1; $x <= 12; $x++)
        {
            $select_month_control.= '<option value="'.$x.'"'.($x != $month ? '' : ' selected="selected"').'>'.date('F',mktime(0,0,0,$x,1,$year)).'</option>';
        }
        $select_month_control.= '</select>';
        
        /* select year control */
        $year_range = 7;
        $select_year_control = '<select name="year" id="year">';
        for($x = ($year-floor($year_range/2)); $x <= ($year+floor($year_range/2)); $x++)
        {
            $select_year_control.= '<option value="'.$x.'"'.($x != $year ? '' : ' selected="selected"').'>'.$x.'</option>';
        }
        $select_year_control.= '</select>';
        
        /* "next month" control */
        $next_month_link = '<a href="?month='.($month != 12 ? $month + 1 : 1).'&amp;year='.($month != 12 ? $year : $year + 1).'" class="control">Next Month >> </a>';
        
        /* "previous month" control */
        $previous_month_link = '<a href="?month='.($month != 1 ? $month - 1 : 12).'&amp;year='.($month != 1 ? $year : $year - 1).'" class="control"> << Previous Month</a>';
        
        /* bringing the controls together */
        $controls = '<form method="get" class="styled-select">'.$select_month_control.'&nbsp;'.$select_year_control.'&nbsp;<input type="submit" name="submit" value="Go" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$previous_month_link.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$next_month_link.'</form>';
        
        return $controls;
    }
    /**
     * to reterive date matched information of
     * event
     * @param string $date
     * @return unknown
    */ 
    public function eventOfDate($date)
    {
        $limit = 3;
        $configLimit = Mage::getStoreConfig(self::LIMIT_EVENTS);
        if ($configLimit != '' AND $configLimit != 0) {
            $limit = $configLimit;
        }
        /* @var $limit intiger configured limit */
        $res = Mage::getSingleton('core/resource');
        $table = $res->getTableName('events/events');
        $read = $res->getConnection('core_read');
        $select = $read->select()->distinct('event_id')->from(array('evts' => $table))
                                    ->where('DATE(evts.event_start_date) <= (?)', $date)
                                    ->where('DATE(evts.event_end_date) >= (?)', $date)
                                    ->where('evts.event_status = (?)', 1)
                                    ->limit($limit);
                             //echo $select;exit;
        $output = $read->fetchAll($select);
        
        return $output;
    }
}
