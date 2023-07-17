<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Student;
use App\Repository\NoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/note')]
class NoteController extends AbstractController
{

    public function __construct(private NoteRepository $repo, private EntityManagerInterface $em)
    {
    }


    #[Route(methods: 'GET')]
    public function all(): JsonResponse
    {
        return $this->json($this->repo->findAll());
    }

    #[Route('/{id}', methods: 'POST')]
    public function add(Student $student, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        try {

            $note = $serializer->deserialize($request->getContent(), Note::class, 'json');
        } catch (\Exception $error) {
            return $this->json('Invalid body', 400);
        }
        $errors = $validator->validate($note);
        if ($errors->count() > 0) {
            return $this->json(['errors' => $errors], 400);
        }
        $note->setStudent($student);
        $note->setCreatedAt(new DateTime());

        $this->em->persist($note);
        $this->em->flush();
        return $this->json($note, 201);


    }

}