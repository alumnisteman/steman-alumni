<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string|null $description
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $image_desktop
 * @property string|null $image_mobile
 * @property string|null $link
 * @property string $position
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property bool $is_active
 * @property int $click
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad position($position)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereImageDesktop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereImageMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ad whereUpdatedAt($value)
 */
	class Ad extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $action
 * @property int|null $user_id
 * @property array<array-key, mixed>|null $meta
 * @property string|null $hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property int $points_required
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge wherePointsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereUpdatedAt($value)
 */
	class Badge extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $category
 * @property string $description
 * @property string|null $discount_info
 * @property string $whatsapp
 * @property string|null $website_url
 * @property string $location
 * @property string|null $logo_url
 * @property string $status
 * @property int $offers_alumni_discount
 * @property string|null $discount_details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BusinessPhoto> $photos
 * @property-read int|null $photos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereDiscountDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereDiscountInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereOffersAlumniDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereWebsiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereWhatsapp($value)
 */
	class Business extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $business_id
 * @property string $photo_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Business $business
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto wherePhotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessPhoto whereUpdatedAt($value)
 */
	class BusinessPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $commentable_id
 * @property string|null $commentable_type
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $commentable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withoutTrashed()
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $subject
 * @property string $message
 * @property string|null $ai_suggested_reply
 * @property int $is_ai_processed
 * @property string|null $reply_content
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $replied_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereAiSuggestedReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereIsAiProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereRepliedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereReplyContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactMessage whereUpdatedAt($value)
 */
	class ContactMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $donation_campaign_id
 * @property numeric $amount
 * @property string $status
 * @property string|null $proof_path
 * @property string|null $hash
 * @property int $is_anonymous
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DonationCampaign $campaign
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereDonationCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereProofPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Donation whereUserId($value)
 */
	class Donation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property string $description
 * @property string|null $bank_info
 * @property numeric $goal_amount
 * @property numeric $current_amount
 * @property string|null $image
 * @property string $status
 * @property bool $is_featured
 * @property string|null $distribution_reports
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Donation> $donations
 * @property-read int|null $donations_count
 * @property-read mixed $progress
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign event()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign foundation()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereBankInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereCurrentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereDistributionReports($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereGoalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DonationCampaign whereUpdatedAt($value)
 */
	class DonationCampaign extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property numeric $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Post|null $post
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feed whereUserId($value)
 */
	class Feed extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $follower_id
 * @property int $following_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $follower
 * @property-read \App\Models\User|null $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereUpdatedAt($value)
 */
	class Follow extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property-read int|null $comments_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $status
 * @property int $views
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read bool $is_active
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum whereViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Forum withoutTrashed()
 */
	class Forum extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $type
 * @property string|null $file_path
 * @property string|null $youtube_url
 * @property string|null $tiktok_url
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $is_published
 * @property-read mixed $tiktok_embed_html
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereTiktokUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereYoutubeUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery withoutTrashed()
 */
	class Gallery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string|null $weight
 * @property string|null $height
 * @property string|null $bmi_category
 * @property string|null $activity_level
 * @property string|null $blood_pressure_status
 * @property string|null $blood_sugar_status
 * @property string|null $cholesterol_status
 * @property string|null $last_symptoms
 * @property string|null $ai_recommendation
 * @property \Illuminate\Support\Carbon|null $last_checkup_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereActivityLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereAiRecommendation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereBloodPressureStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereBloodSugarStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereBmiCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereCholesterolStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereLastCheckupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereLastSymptoms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthProfile whereWeight($value)
 */
	class HealthProfile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string $company
 * @property string|null $location
 * @property string|null $description
 * @property string|null $content
 * @property string|null $external_link
 * @property string $type
 * @property string $status
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $is_active
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereExternalLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JobVacancy withoutTrashed()
 */
	class JobVacancy extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $likeable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereUserId($value)
 */
	class Like extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $group
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Major whereUpdatedAt($value)
 */
	class Major extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sender_id
 * @property int|null $receiver_id
 * @property int|null $parent_id
 * @property string|null $target_year
 * @property string $message
 * @property int|null $is_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $receiver
 * @property-read \App\Models\User|null $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereReceiverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereTargetYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message withoutTrashed()
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property string|null $thumbnail
 * @property string $category
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read bool $is_published
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|News withoutTrashed()
 */
	class News extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $guest_name
 * @property string|null $description
 * @property string $audio_url
 * @property string|null $thumbnail_url
 * @property string|null $duration
 * @property string $category
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $audio_link
 * @property-read mixed $thumbnail_link
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereAudioUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereThumbnailUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Podcast whereUserId($value)
 */
	class Podcast extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $content
 * @property string|null $image_url
 * @property string $type
 * @property string $visibility
 * @property int $is_anonymous
 * @property-read int|null $likes_count
 * @property-read int|null $comments_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Like> $likes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $taggedUsers
 * @property-read int|null $tagged_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCommentsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereIsAnonymous($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereLikesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withoutTrashed()
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $icon
 * @property string|null $content
 * @property string $status
 * @property int $is_event
 * @property string|null $event_date
 * @property string|null $event_location
 * @property int|null $max_slots
 * @property string|null $image
 * @property string|null $registration_link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read bool $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramRegistration> $registrations
 * @property-read int|null $registrations_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereEventLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereIsEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereMaxSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereRegistrationLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Program withoutTrashed()
 */
	class Program extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $program_id
 * @property string $status
 * @property string|null $ticket_code
 * @property string|null $checked_in_at
 * @property string|null $qr_code_path
 * @property string|null $phone_number
 * @property string $motivation
 * @property string|null $attachment_path
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Program|null $program
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereAttachmentPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereMotivation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereProgramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereQrCodePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereTicketCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProgramRegistration withoutTrashed()
 */
	class ProgramRegistration extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $label
 * @property string $group
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $platform
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $icon
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereUserId($value)
 */
	class SocialLink extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string|null $content
 * @property string|null $spotify_url
 * @property string|null $image_url
 * @property string|null $caption
 * @property int $views_count
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereSpotifyUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereViewsCount($value)
 */
	class Story extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $story_id
 * @property int $viewer_id
 * @property string $viewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Story $story
 * @property-read \App\Models\User|null $viewer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView whereStoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView whereViewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoryView whereViewerId($value)
 */
	class StoryView extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $major_year
 * @property string $quote
 * @property string|null $content
 * @property string|null $image_path
 * @property int|null $user_id
 * @property int $is_published
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereMajorYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SuccessStory whereUserId($value)
 */
	class SuccessStory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $tagged_user_id
 * @property string $taggable_type
 * @property int $taggable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $taggable
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTaggableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTaggableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTaggedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $social_id
 * @property string|null $social_type
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $role
 * @property string $status
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Badge> $badges
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_active_at
 * @property string|null $nisn
 * @property string|null $graduation_year
 * @property string|null $major
 * @property string|null $current_job
 * @property string|null $company_university
 * @property string|null $phone_number
 * @property string|null $address
 * @property string|null $bio
 * @property string|null $career_path
 * @property bool $is_mentor
 * @property string|null $mentor_bio
 * @property string|null $mentor_expertise
 * @property string|null $profile_picture
 * @property int $show_social
 * @property string|null $remember_token
 * @property string|null $qr_login_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int $points
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $city_name
 * @property string|null $country_name
 * @property int $mentoring
 * @property-read int|null $badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feed> $feed
 * @property-read int|null $feed_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read int|null $following_count
 * @property-read int|null $age
 * @property-read string $profile_picture_url
 * @property-read \App\Models\HealthProfile|null $healthProfile
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProgramRegistration> $programRegistrations
 * @property-read int|null $program_registrations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialLink> $socialLinks
 * @property-read int|null $social_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Story> $stories
 * @property-read int|null $stories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User online()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBadges($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCareerPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCompanyUniversity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentJob($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGraduationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsMentor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastActiveAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMentorBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMentorExpertise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMentoring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNisn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereQrLoginToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereShowSocial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSocialType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $category
 * @property int $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserInterest whereUserId($value)
 */
	class UserInterest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $target_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $target
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMatch whereUserId($value)
 */
	class UserMatch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $view_time Duration in milliseconds
 * @property int|null $scroll_depth Scroll percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Post|null $post
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView whereScrollDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostView whereViewTime($value)
 */
	class UserPostView extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $total_view_time
 * @property int $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Post|null $post
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary whereTotalViewTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserPostViewSummary whereViewsCount($value)
 */
	class UserPostViewSummary extends \Eloquent {}
}

