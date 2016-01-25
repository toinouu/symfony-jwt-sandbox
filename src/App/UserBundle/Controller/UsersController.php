<?php

namespace App\UserBundle\Controller;

use App\UserBundle\Entity\User;
use App\Util\Controller\AbstractRestController as BaseController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UsersController extends BaseController
{
    /**
     * Lists all users.
     *
     * @Rest\Get("/users")
     * @ApiDoc(
     * 	 section="User",
     * 	 resource=true,
     * 	 statusCodes={
     * 	     200="OK (list all users)",
     * 	     401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @return Doctrine\ORM\QueryBuilder $results
     */
    public function getAllUsersAction()
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('AppUserBundle:User');
        $query = $repo->createQueryBuilder('u')
            ->select('u.id', 'u.email', 'u.firstname', 'u.lastname')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get User by identfier.
     *
     * @Rest\Get("/users/{id}", requirements={"id" = "\d+"})
     * @Rest\View(serializerGroups={"api"})
     * @ApiDoc(
     * 	 section="User",
     * 	 resource=true,
     * 	 statusCodes={
     * 	     200="OK",
     * 	     401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @return array
     */
    public function getUserAction($id)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('AppUserBundle:User');
        $user = $repo->find($id);

        return $user;
    }

    /**
     * Add a follower from current user to another.
     *
     * @Rest\Post("/users/followers/{follower}", requirements={"follower" = "\d+"})
     * @ApiDoc(
     * 	section="User",
     * 	resource=true,
     * 	parameters={
     *     {"name"="follower", "dataType"="integer", "required"=true, "description"="Follower"}
     *   },
     * 	 statusCodes={
     * 	   204="No Content (follower successfully added)",
     * 	   401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function addFollowerAction($follower)
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();

        $follower = $em->getRepository('AppUserBundle:User')->find($follower);
        $user->addFollower($follower);
        $em->flush();

        return $this->handleView(204);
    }

    /**
     * Remove a followed user from the current user.
     *
     * @Rest\Delete("/users/followers/{follower}", requirements={"follower" = "\d+"})
     * @ApiDoc(
     * 	section="User",
     * 	resource=true,
     * 	parameters={
     *     {"name"="follower", "dataType"="integer", "required"=true, "description"="Follower"}
     *   },
     * 	 statusCodes={
     * 	   204="No Content (follower successfully deleted)",
     * 	   401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function removeFollowerAction($follower)
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();

        $follower = $em->getRepository('AppUserBundle:User')->find($follower);
        $user->removeFollower($follower);

        $em->flush();

        return $this->handleView(204);
    }

    /**
     * Add a followed user to the current user.
     *
     * @Rest\Post("/users/follows/{followed}", requirements={"followed" = "\d+"})
     * @ApiDoc(
     * 	section="User",
     * 	resource=true,
     * 	parameters={
     *     {"name"="followerd", "dataType"="integer", "required"=true, "description"="Followed"}
     *   },
     * 	 statusCodes={
     * 	   204="No Content (follow successfully added)",
     * 	   401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function addFollowedAction($followed)
    {
        $em = $this->getEntityManager();
        $user = $this->getCurrentUser();

        $followed = $em->getRepository('AppUserBundle:User')->find($followed);
        $user->addFollow($followed);

        $em->flush();

        return $this->handleView(204);
    }

    /**
     * Lists all followers.
     *
     * @Rest\Get("/users/{id}/followers")
     * @ApiDoc(
     * 	 section="User",
     * 	 resource=true,
     * 	 statusCodes={
     * 	     200="OK (list all followers)",
     * 	     401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @return Doctrine\ORM\QueryBuilder $results
     */
    public function getFollowers($id)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('AppUserBundle:User');
        $user = $repo->find($id);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('Unable to find user with id %d', $id));
        }

        return $user->getFollowers();
    }

    /**
     * Lists all followers.
     *
     * @Rest\Get("/users/{id}/follows")
     * @ApiDoc(
     * 	 section="User",
     * 	 resource=true,
     * 	 statusCodes={
     * 	     200="OK (list all followers)",
     * 	     401="Unauthorized (this resource require an access token)"
     * 	 },
     * )
     *
     * @return Doctrine\ORM\QueryBuilder $results
     */
    public function getFollows($id)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('AppUserBundle:User');
        $user = $repo->find($id);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('Unable to find user with id %d', $id));
        }

        return $user->getFollows();
    }
}
