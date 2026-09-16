import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/formulaire/data/datasources/formulaire_remote_datasource.dart';
import 'package:mobile_technicien/features/formulaire/domain/entities/formulaire.dart';
import 'package:mobile_technicien/features/formulaire/domain/repositories/formulaire_repository.dart';

class FormulaireRepositoryImpl implements FormulaireRepository {
  final FormulaireRemoteDatasource remote;
  FormulaireRepositoryImpl(this.remote);

  @override
  Future<Result<Formulaire>> getFormulaire(int interventionId) async {
    try {
      final formulaire = await remote.getFormulaire(interventionId);
      return Success(formulaire);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> submitFormulaire(
    int interventionId, {
    required Map<int, dynamic> reponses,
    required Map<int, List<String>> fichiers,
  }) async {
    try {
      await remote.submitFormulaire(interventionId, reponses: reponses, fichiers: fichiers);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<void>> finishIntervention(int interventionId, {String? commentaire}) async {
    try {
      await remote.finishIntervention(interventionId, commentaire: commentaire);
      return const Success(null);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
