import 'package:mobile_technicien/core/usecase/result.dart';
import 'package:mobile_technicien/features/home/domain/entities/home_summary.dart';
import 'package:mobile_technicien/features/home/domain/repositories/home_repository.dart';

class GetHomeSummaryUsecase {
  final HomeRepository repository;
  GetHomeSummaryUsecase(this.repository);

  Future<Result<HomeSummary>> call() => repository.getHomeSummary();
}
