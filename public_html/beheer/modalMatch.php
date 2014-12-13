<?
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.match.php');

    $init = new init(1,0,0);
    $settings = new settings();

    $match = new match($settings, $init->errorClass, $init->notificationClass, $init->repository->get_data("matchId"));
    $match->getData();
?>

<div class="modal-dialog">
    <div class="modal-content">
        <form action="compPairing.php" method="post" role="form">
            <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
            <input type="hidden" name="competitie" value="<?php echo $init->repository->get_data("competitie");?>">
            <input type="hidden" name="ronde" value="<?php echo $init->repository->get_data("round");?>">
            <input type="hidden" name="matchId" value="<?php echo $init->repository->get_data("matchId");?>">
            <input type="hidden" name="pgnId" id="pgnId" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php
                    echo $match->playerWhite->name." (".$match->playerWhiteElo.") - ".$match->playerBlack->name." (".$match->playerBlackElo.")";
                ?></h4>
            </div>
            <div class="modal-body" style="overflow: auto;">
                <div class="form-group">
                    <select class="form-control" id="pgnSelect">
                        <option>Nieuw</option>
                        <?php
                            foreach($match->pgnArray as $pgn)
                            {
                                echo '<option value="'.$pgn["id"].'" pgn=\''.$pgn["pgn"].'\'">PGN id '.$pgn["id"].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <textarea class="form-control" rows="10" name="pgnText" id="pgnText"></textarea>
                </div>

                <div class="pull-right btn-group">
                    <a href="compPairing.php" class="btn btn-danger" title="Verwijder PGN" id="pgnRemove"><span class="glyphicon glyphicon-trash"></span></a>
                    <button type="submit" class="btn btn-primary">Opslaan</button>
                </div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <input type="text" class="form-control" id="pgnLink" placeholder="Hier komt de link naar de partij">  
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="pgnIframe" placeholder="Hier komt de code voor de iFrame">
                </div>
            </div>
        </form>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

