<?php
/**
 * TAPIFY Social Publishing — provider factory.
 * The one place that knows concrete providers. Add a platform = a case here.
 * Note: Instagram is authorized through the Meta (facebook) flow, so its OAuth
 * uses FacebookProvider; only its publish() differs (added with InstagramProvider).
 */
class SocialProviderFactory
{
    public static function make($platform)
    {
        switch (strtolower(trim($platform))) {
            case 'facebook':  return new FacebookProvider();
            case 'instagram': return new InstagramProvider();
            // case 'linkedin':  return new LinkedInProvider();   // next phase
            default:
                throw new SocialException('That platform is not available yet.', 400, "unknown platform '{$platform}'");
        }
    }

    /** Platforms a user can start an OAuth connect for right now. */
    public static function connectable()
    {
        return ['facebook']; // grows: instagram (via meta), linkedin
    }
}
