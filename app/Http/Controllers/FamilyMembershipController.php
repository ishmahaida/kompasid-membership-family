<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PostMetaModel;
use App\PostModel;
use Illuminate\Support\Facades\DB;

class FamilyMembershipController extends Controller
{

	public $userid;

	public function __construct()
	{
        #pake email karena ga bisa ngecek pake id login
        #karena post author-nya ga berubah
        #jadi ngecek siapa ownernya pake email (post_title)

		// $this->userid = 1241497;
        // $this->userid = 1399792;

        // $this->useremail = 'glory.tannia@kompas.com';
        $this->useremail = 'Sumampir Alliance';
	}

    /**
    * func to get owner from a membership
    * @param int $userid
    * @return object
    */
    public function  getOwnerMembership()
    {
    	#harusnya dari parameter
        $posts = PostModel::select('id', 'post_author', 'post_date_gmt', 'post_title', 'post_parent', 'post_type')
        					->where('post_type', 'wc_memberships_team')
        					->where('post_author', $this->userid)
        					->get();

        return $posts;
    }


    /**
    * func to get id wp_posts owner
    * @param int $userid
    * @return int
    */
    public function getIdPostOwner($userid)
    {
        $query = PostModel::select('ID')->where('post_author', '=', $userid)->where('post_type', '=', 'wc_memberships_team')->first();

        return $query->ID;
    }


    /**
    * func to insert into posts with post type = wc_team_invitation
    * @param request $request
    * @return bool
    */
    public function giveMembershipToTeam(Request $request)
    {
    	$message = "";
    	$emailMember = $request->email_member;
        $ownerid = $this->getIdPostOwner($this->userid);

    	#harusnya dari parameter id login (owner)
    	$ins = PostModel::insertGetId([
    		'post_author' => $this->userid,
    		'post_date' => date('Y-m-d H:i:s'),
    		'post_date_gmt' => date('Y-m-d H:i:s'),
    		'post_content' => '',
    		'post_title' => $emailMember,
    		'post_excerpt' => '',
    		'post_status' => "wcmti-accepted",
    		'comment_status' => "closed",
    		'ping_status' => "closed",
    		'post_password' => '', #isinya apa?
    		'post_name' => '', #isinya apa?
    		'to_ping' => '',
    		'pinged' => '',
    		'post_modified' => date('Y-m-d H:i:s'),
    		'post_modified_gmt' => date('Y-m-d H:i:s'),
    		'post_content_filtered' => '',
    		'post_parent' => $ownerid, #diisi dengan ID dari tabel post owner-nya
    		'guid' =>  '', #diisi apa?
    		'menu_order' => 0,
    		'post_type' => "wc_team_invitation",
    		'post_mime_type' => "member",
    		'comment_count' => 0
    	]);
    	
    	if(empty($ins) || is_null($ins) || $ins == 0)
    	{
    		$message = "Failed giving membership to team.";
    		return $this->error(400, $message, []);
    	}
    	else
    	{
    		$message = "Success giving membership to team.";
    		return $this->success(200, $message, []);
    	}
    }

    /**
    * func to update wp_post, changing role owner/team
    * @param request $request
    * @return bool
    */
    public function changeRole(Request $request)
    {
    	$message = "";
    	$emailMember = $request->email_member;

    	$currentRole = $this->checkCurrentRole($this->useremail);

        try{
            if($currentRole->post_type == 'wc_memberships_team')
            {
                #change current owner to invitation, by useremail (post title)
                $upd_owner_to_team = PostModel::where('post_type', 'wc_memberships_team')->where('post_title', $this->useremail)->update([
                    'post_type' => 'wc_team_invitation',
                    'post_modified' => date('Y-m-d H:i:s'),
                    'post_modified_gmt' => date('Y-m-d H:i:s')
                ]);

                #change chosen email type to owner
                $upd_team_to_owner = PostModel::where('post_title', $emailMember)->update([
                    'post_type' => 'wc_memberships_team',
                    'post_modified' => date('Y-m-d H:i:s'),
                    'post_modified_gmt' => date('Y-m-d H:i:s')
                ]);

                $message = "Success updated team to owner";

                return $this->success(200, $message, []);
            }
            else
            {
                $message = "Can not update to owner.";
                return $this->error(400, $message, []);
            }
        }
        catch(\Exception $e)
        {
            $message = $e->getMessage();
            return $this->error(400, $message, []);
        }

    	

    	
    }

    /**
    * func to delete team(member) by owner
    * @param request $request
    * @return bool
    */
    public function deleteMember(Request $request)
    {

        $emailMember = $request->email_member;

        $currentRole = $this->checkCurrentRole($this->useremail);

        try{
            #only owner can delete member
            if($currentRole->post_type == 'wc_memberships_team')
            {
                $upd_post_status = PostModel::where('post_title', '=', $emailMember)->update([
                    'post_status' => 'wcmti-cancelled',
                    'post_modified' => date('Y-m-d H:i:s'),
                    'post_modified_gmt' => date('Y-m-d H:i:s')
                ]);

                $message = "Success deleted team";

                return $this->success(200, $message, []);
            }
            else
            {
                $message = "Only owner can delete member.";
                return $this->error(400, $message, []);
            }
        }
        catch(\Exception $e)
        {
            $message = $e->getMessage();
            return $this->error(400, $message, []);
        }
    }


    /**
    * func to check role owner from userid
    * @param string $useremail
    * @return bool
    */
    public function checkCurrentRole($useremail)
    {
    	$post_type = PostModel::where('post_title', '=', $useremail)->select('post_type')->first();

    	return $post_type;
    }
}