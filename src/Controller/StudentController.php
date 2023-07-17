<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;

#[Route('/api/student')]
class StudentController extends AbstractController
{
    public function __construct(private StudentRepository $repo, private EntityManagerInterface $em)
    {
    }
    #[Route(methods: 'GET')]
    public function all(): Response
    {
        return $this->json($this->repo->findAll());
    }

    #[Route('/{id}', methods: 'GET')]
    public function one(Student $student): JsonResponse
    {

        return $this->json($student);
    }


    #[Route(methods: 'POST')]
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {


        try {

            $student = $serializer->deserialize($request->getContent(), Student::class, 'json');
        } catch (\Exception $error) {
            return $this->json('Invalid body', 400);
        }
        $errors = $validator->validate($student);
        if ($errors->count() > 0) {
            return $this->json(['errors' => $errors], 400);
        }
        $this->em->persist($student);
        $this->em->flush();
        return $this->json($student, 201);
    }
    #[Route('/{id}', methods: 'DELETE')]
    public function delete(Student $student): JsonResponse
    {

        $this->em->remove($student);

        $this->em->flush();

        return $this->json(null, 204);
    }
    #[Route('/{id}', methods: 'PATCH')]
    public function update(Student $student, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        try {

            $student = $serializer->deserialize($request->getContent(), Student::class, 'json');
        } catch (\Exception $error) {
            return $this->json('Invalid body', 400);
        }
        $errors = $validator->validate($student);

        if ($errors->count() > 0) {
            return $this->json(['errors' => $errors], 400);
        }

        $this->em->flush();
        return $this->json($student, 201);
    }



}