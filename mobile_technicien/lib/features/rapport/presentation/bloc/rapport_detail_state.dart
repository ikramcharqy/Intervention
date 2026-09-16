part of 'rapport_detail_bloc.dart';

sealed class RapportDetailState extends Equatable {
  const RapportDetailState();

  @override
  List<Object?> get props => [];
}

class RapportDetailLoading extends RapportDetailState {
  const RapportDetailLoading();
}

class RapportDetailError extends RapportDetailState {
  final String message;
  const RapportDetailError(this.message);

  @override
  List<Object?> get props => [message];
}

class RapportDetailLoaded extends RapportDetailState {
  final Rapport rapport;
  final bool downloadingPdf;
  final Uint8List? pdfBytes;
  final String? pdfError;

  const RapportDetailLoaded(this.rapport, {this.downloadingPdf = false, this.pdfBytes, this.pdfError});

  RapportDetailLoaded copyWith({bool? downloadingPdf, Uint8List? pdfBytes, String? pdfError}) {
    return RapportDetailLoaded(
      rapport,
      downloadingPdf: downloadingPdf ?? this.downloadingPdf,
      pdfBytes: pdfBytes,
      pdfError: pdfError,
    );
  }

  @override
  List<Object?> get props => [rapport, downloadingPdf, pdfBytes, pdfError];
}
