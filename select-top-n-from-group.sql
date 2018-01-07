SET @current_terr = 0;
SET @terr_rank = 0;
SELECT designacao_id, designacao_territorio, designacao_entrega, designacao_devolucao, designacao_comentario
, @terr_rank := IF(@current_terr = designacao_territorio, @terr_rank + 1, 1) AS terr_rank
, @current_terr := designacao_territorio as current_terr
  FROM designacoes
  ORDER BY designacao_territorio ASC, designacao_entrega DESC