function send_edit_request(path) {
    show_busy()
    element = "edit_space"
    xmlHttp=GetXmlHttpObject(stateChanged)
    xmlHttp.open("GET", path , false)
    xmlHttp.send(null)
    MicroModal.show('modal-edit-game')
    hide_busy()
}

function send_submit_request(path, element_name) {
    show_busy()
    element = element_name
    xmlHttp=GetXmlHttpObject(stateChanged)
    xmlHttp.open("GET", path , false)
    xmlHttp.send(null)
    hide_busy()
    MicroModal.close('modal-edit-game')
    clear_edit()
}

function clear_edit() {
    send_edit_request("edit_game.php")
} 

// ------------------------------------
// Moderators
// ------------------------------------

function edit_mod() {
    send_edit_request("edit_game.php?q=e_moderator&game_id="+game_id)
}

function submit_Moderators() {
    moderators = ""
    mySelect = document.change_mod.elements[0]
    count=0
    for ( i=0; i<mySelect.options.length; i++ ) {
        if ( mySelect.options[i].selected) {
            if (count == 0 ) {
            moderators = mySelect.options[i].value
            } else {
            moderators = moderators+","+mySelect.options[i].value
            }
            count++
        }
    }
    send_submit_request("/edit_game.php?&q=s_moderator&modlist="+moderators+"&game_id="+game_id, "mod_td")
}

// ------------------------------------
// Dates
// ------------------------------------

function edit_dates() {
    send_edit_request("/edit_game.php?q=e_date&game_id="+game_id)
}

function submit_dates() {
    s_date = document.edit_date.start.value
    if ( ! isDate(s_date, "yyyy-MM-dd") ) {
        alert ("Start date is not a valid sql date.\nyyyy-mm-dd")
        hide_busy()
        return false
    }
    stime = document.edit_date.start_time.value
    e_date = document.edit_date.end.value
    if ( e_date != "" && e_date != "0000-00-00" && !isDate(e_date, "yyyy-MM-dd") ) {
        alert ("End date is not a valid sql date.\nyyyy-mm-dd")
        hide_busy()
        return false
    }
    swf = document.edit_date.swf.value
    if ( document.edit_date.swf.checked ) {
        swf = "Yes"
    }
    send_submit_request(
        "/edit_game.php?q=s_date&sdate="+s_date+"&stime="+stime+"&edate="+e_date+"&swf="+swf+"&game_id="+game_id,
        "date_td"
    )
}

// ------------------------------------
// Description
// ------------------------------------

function edit_desc() {
    send_edit_request("/edit_game.php?q=e_description&game_id="+game_id)
}

function submit_desc() {
    descrip = document.new_descrip.desc.value
    send_submit_request(
        "/edit_game.php?q=s_description&desc="+descrip+"&game_id="+game_id,
        "desc_td"
    )
}

// ------------------------------------
// Status
// ------------------------------------

function edit_status() {
    send_edit_request("/edit_game.php?q=e_status&game_id="+game_id)
}

function submit_status() {
    I = document.new_status.status.selectedIndex
    s = document.new_status.status.options[I].value
    p = document.new_status.phase.value
    d = document.new_status.day.value
    isgood =  true
    if ( s == "In Progress" && currentStatus != "In Progress" ) {
        isgood = confirm("Have you changed the BGG Thread id from the sign-up thread to the Game thread?  You must do this first before changing the status for the cassandra files to update correctly.")
    }
    if ( isgood ) {
        send_submit_request(
            "/edit_game.php?q=s_status&status="+s+"&phase="+p+"&day="+d+"&game_id="+game_id,
            "status_td"
        )
    }
    if ( currentStatus != s ) {
        location.href=myURL
    }
} 

// ------------------------------------
// Speed
// ------------------------------------

function edit_speed() {
    send_edit_request("/edit_game.php?q=e_speed&game_id="+game_id)
}

function submit_speed(){
    speed = document.new_speed.speed.value
    send_submit_request("/edit_game.php?q=s_speed&speed="+speed+"&game_id="+game_id, "speed_td")
    send_submit_request("/edit_game.php?q=s_deadline&speed="+speed+"&game_id="+game_id, "deadline_td")
    send_submit_request("/edit_game.php?q=s_date&speed="+speed+"&game_id="+game_id, "date_td")
    
}

// ------------------------------------
// Deadline
// ------------------------------------

function edit_deadline() {
    send_edit_request("/edit_game.php?q=e_deadline&game_id="+game_id)
}

function submit_deadline() {
    lynch = document.new_deadline.lynch.value
    night = document.new_deadline.night.value
    day_length = document.new_deadline.day_length.value
    night_length = document.new_deadline.night_length.value

    send_submit_request(
        "/edit_game.php?q=s_deadline&lynch="+lynch+"&night="+night+"&day_length="+day_length+"&night_length="+night_length+"&game_id="+game_id,
        "deadline_td"
    )
} 

// ------------------------------------
// Winner
// ------------------------------------

function edit_winner() {
    send_edit_request("/edit_game.php?q=e_winner&game_id="+game_id)
}

function submit_winner() {
    I = document.new_winner.winner.selectedIndex
    w = document.new_winner.winner.options[I].value

    send_submit_request(
        "/edit_game.php?q=s_winner&winner="+w+"&game_id="+game_id,
        "win_td"
    )
}

// ------------------------------------
// Subthreads
// ------------------------------------

function edit_subt() {
    send_edit_request("/edit_game.php?q=e_subthread&game_id="+game_id)
}

function delete_subt(t_id) {
    if ( confirm("Are you sure you want to delete this sub-thread?\nSaying yes will delete the game information, and all posts from the database.") ) {
        send_submit_request(
            "/edit_game.php?q=d_subthread&thread_id="+t_id+"&game_id="+game_id,
            "subt_td"
        )
    }
}

function add_subt() {
    t_id = document.new_subt.tid.value
    if ( ! isNumber(t_id) ) {
        alert ("This nees to be a BGG thread_id (numbers only)")
        return 
    }
    
    send_submit_request(
        "/edit_game.php?q=a_subthread&thread_id="+t_id+"&game_id="+game_id,
        "subt_td"
    )
    alert("Please edit the specifics of the sub-thread on it's own page")
}

// ------------------------------------
// Name
// ------------------------------------

function edit_name() {
    send_edit_request("/edit_game.php?q=e_name&game_id="+game_id)
}

function submit_name() {
    t = document.new_title.title.value
    send_submit_request("/edit_game.php?q=s_name&title="+t+"&game_id="+game_id, "name_span")
} 

// ------------------------------------
// Thread (ID)
// ------------------------------------

function edit_thread() {
    send_edit_request("/edit_game.php?q=e_thread&game_id="+game_id)
}

function submit_thread() {
    th = document.new_thread.thread.value
    if ( ! isNumber(th) ) {
        alert ("This needs to be a BGG thread_id (numbers only)")
        return false
    }
    send_submit_request(
        "/edit_game.php?q=s_thread&thread_id="+th+"&game_id="+game_id,
        "thread_td"
    )

    myLink = document.getElementById('game_link')
    myLink.href = "http://www.boardgamegeek.com/thread/"+th

    alert("Since you changed the thread_id the page you are on is no longer a valid page, so hiting refresh will not work.")
} 

// ------------------------------------
// Max Players
// ------------------------------------

function edit_maxplayers() {
    send_edit_request("/edit_game.php?q=e_maxplayers&game_id="+game_id)
}

function submit_maxplayers() {
    mp = document.change_maxp.max_players.value
    if ( ! isNumber(mp) ) {
        alert("This needs to be a number")
        hide_busy()
        return false
    }
    send_submit_request(
        "/edit_game.php?q=s_maxplayers&max_players="+mp+"&game_id="+game_id,
        "td_maxplayers"
    )
}

// ------------------------------------
// Complexity
// ------------------------------------

function edit_complex() {
    send_edit_request("/edit_game.php?q=e_complex&game_id="+game_id)
}

function submit_complex() {
    comp = document.comp_form.complex.value
    send_submit_request(
        "/edit_game.php?q=s_complex&complex="+comp+"&game_id="+game_id,
        "td_complex"
    )
} 

// ------------------------------------
// Add/Edit/Replace/Remove Player
// ------------------------------------

function edit_player(uid,row) {
    send_edit_request("/edit_game.php?q=e_player&uid="+uid+"&row="+row+"&game_id="+game_id)
}

function add_player() {
    send_edit_request("/edit_game.php?q=a_player&game_id="+game_id)
}

function delete_replacement(replace_id) {
    r = document.editPlayer.row_id.value
    c = 0
    element = "r"+r+"_c"+c
    user_id = document.editPlayer.user_id.value

    send_submit_request(
        "/edit_game.php?q=d_replace&user_id="+user_id+"&replace_id="+replace_id+"&game_id="+game_id,
        element
    )
}

function submit_player() {
    uid = document.editPlayer.user_id.value
    rep_id = document.editPlayer.new_rep.options[document.editPlayer.new_rep.selectedIndex].value
    rep_p = document.editPlayer.rep_period.options[document.editPlayer.rep_period.selectedIndex].value
    rep_n = document.editPlayer.rep_number.value
    if ( rep_id != "0" ) {
        if ( rep_n == "" ) {
            alert("You need to specify which "+rep_p+" the player was replaced.")
            return false
        }
        if ( ! isNumber(rep_n) ) {
            alert("You need to enter a number for which "+rep_p+" the player was replaced.")
            return false
        }
    }
    player_alias = document.editPlayer.player_alias.value
    alias_color = encodeURIComponent(document.editPlayer.alias_color.value, "UTF-8");
    r_name = document.editPlayer.role_name.value
    r_id = document.editPlayer.role_type.options[document.editPlayer.role_type.selectedIndex].value
    s = document.editPlayer.side.options[document.editPlayer.side.selectedIndex].value
    death_p = document.editPlayer.d_phase.value
    death_d = document.editPlayer.d_day.value
    note = document.editPlayer.comment.value

    send_submit_request(
        "/edit_game.php?q=s_player&uid="+uid+"&rep_id="+rep_id+"&rep_p="+rep_p+"&rep_n="+rep_n+"&player_alias="+player_alias+"&alias_color="+alias_color+"&r_name="+r_name+"&r_id="+r_id+"&side="+s+"&d_phase="+death_p+"&d_day="+death_d+"&comment="+note+"&game_id="+game_id,
        "player_table"
    )
}

function submit_new_player() {
    uid = document.getElementById('player_id_new_p').value
    s = "old"
    if ( uid == "" ) {
        if ( confirm("This is a new player correct?") ) {
            uid = document.getElementById('player_name_new_p').value
            s = "new"
        } else {
            return false;
        }
    }
    send_submit_request(
        "/edit_game.php?q=an_player&user_id="+uid+"&s="+s+"&game_id="+game_id,
        "player_table"
    )
}

function delete_player() {
    uid = document.editPlayer.user_id.value
    send_submit_request(
        "/edit_game.php?q=d_player&user_id="+uid+"&game_id="+game_id,
        "player_table"
    )
}

// ------------------------------------
// Edit Player Info
// ------------------------------------

function edit_alias() {
    send_edit_request("/edit_game.php?q=e_alias&game_id="+game_id)
}

function edit_rolename() {
    send_edit_request("/edit_game.php?q=e_rolename&game_id="+game_id)
}

function edit_roletype() {
    send_edit_request("/edit_game.php?q=e_roletype&game_id="+game_id)
}

function edit_teams() {
    send_edit_request("/edit_game.php?q=e_team&game_id="+game_id)
}

function edit_comments() {
    send_edit_request("/edit_game.php?q=e_comments&game_id="+game_id)
} 

function submit_alias() {
    num = document.change_aliases.elements.length-1
    aliases = ""
    colors = ""
    for ( i=0; i<num; i++ ) {
        aliases += document.change_aliases.elements[i].value
        i++;
        colors += encodeURIComponent(document.change_aliases.elements[i].value, "UTF-8");
        if ( i != (num-1) ) { 
            aliases += "," 
            colors += ","
        }
    }
    send_submit_request(
        "/edit_game.php?q=s_alias&aliases="+aliases+"&colors="+colors+"&game_id="+game_id,
        "player_table"
    )
}

function submit_rolename() {
    num = document.change_rolenames.elements.length-1
    rnames = ""
    for ( i=0; i<num; i++ ) {
        rnames += document.change_rolenames.elements[i].value
        if ( i != (num-1) ) { rnames += "," }
    }
    send_submit_request(
        "/edit_game.php?q=s_rolename&rnames="+rnames+"&game_id="+game_id,
        "player_table"
    )
}

function submit_roletype() {
    num = document.change_roletypes.elements.length-1
    rtypes = ""
    for ( i=0; i<num; i++ ) {
        rtypes += document.change_roletypes.elements[i].options[document.change_roletypes.elements[i].selectedIndex].value
        if ( i != (num-1) ) { rtypes += "," }
    }
    send_submit_request(
        "/edit_game.php?q=s_roletype&rtypes="+rtypes+"&game_id="+game_id,
        "player_table"
    )
}

function submit_team() {
    num = document.change_teams.elements.length-1
    teams = ""
    for ( i=0; i<num; i++ ) {
        teams += document.change_teams.elements[i].options[document.change_teams.elements[i].selectedIndex].value
        if ( i != (num-1) ) { teams += "," }
    }
    send_submit_request(
        "/edit_game.php?q=s_team&teams="+teams+"&game_id="+game_id,
        "player_table"
    )
}

// ------------------------------------
// Player Table Deaths
// ------------------------------------

function edit_deaths() {
    send_edit_request("/edit_game.php?q=e_deaths&game_id="+game_id)
}

function submit_deaths() {
    num = document.change_deaths.elements.length-1
    phases = ""
    days = ""
    for ( i=0; i<num; i++ ) {
        phases += document.change_deaths.elements[i].value
        i++
        days += document.change_deaths.elements[i].value
        if ( i != (num-1) ) { 
            phases += "," 
            days += ","
        }
    }
    send_submit_request(
        "/edit_game.php?q=s_deaths&phases="+phases+"&days="+days+"&game_id="+game_id,
        "player_table"
    )
}
