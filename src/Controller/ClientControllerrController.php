<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Annonces;
use App\Form\Annonces1Type;
use App\Form\AvisType;
use App\Form\RechercheAnnonceType;
use App\Repository\AnnoncesRepository;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Swift_Message ; 
use Swift_Mailer ;
use Dompdf\Dompdf;
use Dompdf\Options;





#[Route('/client')]
class ClientControllerrController extends AbstractController
{

    

    
    #[Route('/', name: 'app_client_controllerr_index', methods: ['GET','Post'])]
    public function index(AnnoncesRepository $annoncesRepository,Request $request): Response
    {
        $annoncesRepository=$this->getDoctrine()->getRepository(Annonces::class);
        $search=$this->createForm(RechercheAnnonceType::class);
        $search->handleRequest($request); 
        $s=$annoncesRepository->findAll();
        if ($search->isSubmitted() ) {
            $region=$search['region']->getData(); 
            $result=$annoncesRepository->findAnnonce($region); 
        return $this->renderForm('client_controllerr/index.html.twig',
        ['annonces'=>$result
        ,'search'=>$search]);
        }

        return $this->renderForm('client_controllerr/index.html.twig', [
            'annonces' =>$s,
            'search'=>$search

        ]);
    }

    #[Route('/new', name: 'app_client_controllerr_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnnoncesRepository $annoncesRepository , SluggerInterface $slugger, Swift_Mailer $mailer): Response
    {
        $annonce = new Annonces();
        $form = $this->createForm(Annonces1Type::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            $dompdf = new Dompdf($pdfOptions);
            $html = $this->renderView('client_controllerr/imprimer.html.twig', [
               'annonce' => $annonce]);
       
               $dompdf->loadHtml($html);
               $dompdf->setPaper('A4', 'portrait');
    
               $dompdf->render();
               $dompdf->stream("“mypdf.pdf", ["Attachment" => true]);
              
               $output = $dompdf->output();    
           
            $message = (new Swift_Message('Hello Email')) 
            ->setFrom('mohamedamine.snoussi@esprit.tn') 
            ->setTo($annonce->getEmail()) 
            ->setBody(
                $this->renderView('client_controllerr/mailling.html.twig'),
            
            'text/html'
            );
            $attachement = new \Swift_Attachment($output, "Liste des candidats.pdf", 'application/pdf' );
            $message->attach($attachement);
            
            $mailer->send($message) ;
            
            $brochureFile = $form->get('image')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
              

                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
              //  dd($originalFilename);

                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
              


                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('image_derctory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $annonce->setImage($newFilename);
            $annoncesRepository->save($annonce, true);

            return $this->redirectToRoute('app_client_controllerr_index', [], Response::HTTP_SEE_OTHER);
        }
    }
        return $this->renderForm('client_controllerr/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_client_controllerr_show', methods: ['GET'])]
    public function show(Annonces $annonce): Response
    {


            return $this->render('client_controllerr/show.html.twig', [
                'annonce' => $annonce,
            ]);
        
        
    }

    #[Route('/{id}/edit', name: 'app_client_controllerr_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonces $annonce, AnnoncesRepository $annoncesRepository): Response
    {
        $form = $this->createForm(Annonces1Type::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annoncesRepository->save($annonce, true);

            return $this->redirectToRoute('app_client_controllerr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client_controllerr/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_controllerr_delete', methods: ['POST'])]
    public function delete(Request $request, Annonces $annonce, AnnoncesRepository $annoncesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $annoncesRepository->remove($annonce, true);
        }

        return $this->redirectToRoute('app_client_controllerr_index', [], Response::HTTP_SEE_OTHER);
    }
  
    #[Route('/newavis/{id}', name: 'app_avis_newclient', methods: ['GET', 'POST'])]
    public function newavis(Request $request, AvisRepository $avisRepository, Annonces $annonce): Response
    {
        $avi = new Avis();
        $avi->setAnnonces($annonce);
        $form = $this->createForm(AvisType::class, $avi);
       $form->handleRequest($request);
   
        if ($form->isSubmitted() && $form->isValid()) {
            $avisRepository->save($avi, true);
       
            return $this->redirectToRoute('app_client_controllerr_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

      
  
   
    
    


}
