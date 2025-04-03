<?php
session_start();
$data = $_SESSION['compare_data'] ?? [];
$annee = $_SESSION['compare_annee'] ?? date('Y');
$info = $_SESSION['info'] ?? null;
unset($_SESSION['info']); // on le consomme
?>

<h2>Comparaison Annuelle Agent vs Client - Année <?= htmlspecialchars($annee) ?></h2>
<?php if($info): ?>
  <p style="color:green;"><?= htmlspecialchars($info) ?></p>
<?php endif; ?>

<table border="1" cellpadding="6" style="border-collapse: collapse;">
    <tr style="background:#f0f0f0;">
        <th>Client</th>
        <th>Conso Agent</th>
        <th>Conso Client</th>
        <th>Ecart</th>
        <th>Action</th>
    </tr>
    <?php foreach($data as $row): ?>
    <?php
       $ecart = $row['ecart'];
       $style = ($ecart > 50) ? 'color:red;font-weight:bold;' : '';
    ?>
    <tr>
        <td><?= htmlspecialchars($row['nom'].' '.$row['prenom']) ?></td>
        <td><?= htmlspecialchars($row['conso_agent']) ?></td>
        <td><?= htmlspecialchars($row['conso_client']) ?></td>
        <td style="<?= $style ?>"><?= $ecart ?></td>
        <td>
          <?php if ($ecart > 50): ?>
            <form method="post" action="../traitement/routeur.php?action=creer_facture_plus">
               <input type="hidden" name="client_id" value="<?= $row['client_id'] ?>">
               <input type="hidden" name="annee" value="<?= $row['annee'] ?>">
               <input type="hidden" name="ecart" value="<?= $ecart ?>">
               <button type="submit">Créer Facture +</button>
            </form>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
