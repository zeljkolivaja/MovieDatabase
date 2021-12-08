<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{

    //used in onAuthenticationSuccess method to get the url path (if we are not logged in and we try to visit forbidden page)
    use TargetPathTrait;

    public function __construct(private UserRepository $userRepository, private RouterInterface $router)
    {
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        //this object is container for "badges", for basic use we need user and credentials badge
        return new Passport(

            //if we pass callback as second argument to UserBadge method we can query for User manualy
            //in this case, for demonstration, we queried for user  same way the symfony would(if we only passed $email to UserBadge method)
            new UserBadge($email, function ($email) {
                $user = $this->userRepository->findOneBy(['email' => $email]);
                if (!$user) {
                    throw new UserNotFoundException();
                }
                return $user;
            }),
            //compares user submited password to hashed one stored in DB
            new PasswordCredentials($password),
            [
                //badge that validates submited csrf token coming from login form
                new CsrfTokenBadge(
                    'authenticate',
                    $request->request->get('_csrf_token')
                ),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        //if anon user tries to go somewhere forbiden he will be redirected to login page, but if he then logs in and is authorized to visit
        //that page we redirect him there using this if statement (initial url he tried to visit is stored in the session, we check is it set and then redirect him)
        if ($target = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }

        return new RedirectResponse(
            $this->router->generate('app_homepage')
        );
    }


    protected function getLoginUrl(Request $request): string
    {
        return   $this->router->generate('app_login');
    }
}
