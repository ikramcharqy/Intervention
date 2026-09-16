import 'package:dio/dio.dart';
import 'package:mobile_technicien/core/network/dio_error_mapper.dart';
import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/rapport/data/datasources/rapport_remote_datasource.dart';
import 'package:mobile_technicien/features/rapport/domain/entities/rapport.dart';
import 'package:mobile_technicien/features/rapport/domain/repositories/rapport_repository.dart';

class RapportRepositoryImpl implements RapportRepository {
  final RapportRemoteDatasource remote;
  RapportRepositoryImpl(this.remote);

  @override
  Future<Result<Rapport>> getRapportByIntervention(int interventionId) async {
    try {
      final rapport = await remote.getRapportByIntervention(interventionId);
      return Success(rapport);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }

  @override
  Future<Result<List<int>>> getRapportPdfBytes(int rapportId) async {
    try {
      final bytes = await remote.getRapportPdfBytes(rapportId);
      return Success(bytes);
    } on DioException catch (e) {
      return Failed(mapDioError(e));
    }
  }
}
